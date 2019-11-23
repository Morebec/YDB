# Querying

Querying corresponds to the retrieval of data in the database. YDB provides a simple programatic interface called PYQL (pronounced Pee-Why-Kyu-El, which stands for PHP YDB Query Language) in order to retrieve data programatically. See the user documentation for more info on usage.

## PYQL
PYQL is inspired by the delcarative nature of SQL. It provides using a PHP interface ways to query data.


## Query Planner & Query Executor
Usually in a RDBMS the process of querying data is as follows:

1. Analysis: First, the SQL query is sent to the DBMS for syntaxic analysis. To make sure the query is valid and syntactically correct. This syntax is then converted to language tokens, taht can be used by the database to detect meaning behind the SQL string.
1. Optimization: The SQL query is then optimized for performance. For example, the following query:
```
SELECT * FROM 'user' WHERE 'age' BETWEEN 25 AND 30; 
```
could be optimized like so:
```
SELECT * FROM 'user' WHERE 'age' >= 25 AND 'age' <= 30;
```
1. Planning: Then, the database engine with the use of a Query Planner, will find out the best way to perform the query. Is it by doing a full table scan, an index scan, or a seek scan? This step, answers all these questions by generating multiple plans and determining the best one to use.
1. Execution: Once the best plan has been determined, the query is executed and the results returned.

## YDB + (P)YQL
In YDB, a similar procedure is used to query data. However, since YDB is designed for smaller datasets, it relies on a much simpler way:
1. Analysis: For the moment, since there is only a direct PHP interface to YQL, there is no particular analysis that needs to be done. the PHP interface will generate the appropriate expression tree for the Query Engine to understand the meaning behind the query.
1. Optimization: There is currently no optimization done, it is to the users to ensure that the queries they are feeding the database are optimized.
1. Planning: YDB uses a QueryPlanner in order to determine the best plan for to execute the query.
1. Execution: The generated plan  is executed.

The following sections will discuss these steps in more detail.

### Analysis
The YQL language currently only has a PHP interface implementation, by that we mean that in order to generate YQL Expressions one need to use the `ExpressionBuilder` class methods.
Ther is so far, no way of passing expression strings that will be converted to PHP method calls.

*Note*: Although YQL ends with *'QL'* which reminds of *SQL*, it is in now way compatible with it. It is only inspired by it to be a declarative language, that ressembles a natural language.

Let's review the different elements that make up a query:
```sql
FIND records WHERE 'column' == 'value' AND 'column2' == 'value' FROM table_name;
```

**Query**: A query is a concept that encompases the idea of the need to retreive data in the database.
It has the following attributes:
    - It targets a specific table
    - It has an expression
In the case of the previous snippet of code: the whole line would be a query.

**Expression**: an expression is simply a line of YQL code made of multiple clauses.
In the case of the previous snippet of code: the whole line would be an expression

**Clause**: A clause always start with a keyword, such as `FIND`, `WHERE`, `OR`, `AND`, `FROM` and is followed by a term. Clauses are chained using *Expression Operators* which are also keywords.
In the case of the previous snippet of code, these would be the clauses:
- `FIND records`
- `WHERE column == 'value'`
- `AND 'column' == 'value'`
- `FROM table_name`

**Expression Operator**: An Expression Operator's function is to join clauses together. There are currently two operators: `OR` and `AND`.

**Term**: A term is a subset of an expression. It is usually of the form:
```left_operand term_operator right_operand```
such as
```'column' == 'value' ```

In the code several classes or used to represent theses elements:
- `Morebec\YDB\Entity\Query\Term` represents a term
- `Morebec\YDB\Entity\Query\Operator` represents a Term Operator
- `Morebec\YDB\YQL\ExpressionNode` represents an expression
- `Morebec\YDB\YQL\ExpressionOperator` represents an Expression Operator

Since by design, it is only possible to query records from one table at a time,
the `FIND records` and `FROM table` clauses are not specifically represented, 
instead they are implied when calling methods like `Database::queryTable`.

In code, an expression is represented as a Binary tree.

The following query that fetches books that have a price of 2.00$ or that are of the genre 'adventure':
```sql
FIND records WHERE price == 2.00 OR genre == 'adventure';
```

Is represented using the following tree structure:

```
ExpressionNode(root)
    - left: TermNode(price == 2.00)
    - operator: ExpressionOperator(OR)
    - right: TermNode(genre == 'adventure')
```
Using this structure allow us to traverse the tree and work on every units independently.

### Optimization
There are currently no optimization processes, although we would love to implement this in the future to make queries more performant.
Things like:
```sql
FIND records WHERE price === 2.00 AND/OR price === 2.00
```
Could be optimized easily.

### Query Planning
There are many strategies to resolve a query:

- **Full Table Scan**: This means loading every record in the table, and checking if it matches the query. This is strategy can prove to be quite fast when handling small tables, but painfully slow for tables with a lot of records.

- **Index scan**: Using indexes to determine the records that are potential candidates for the query based on their values. For large tables this is always faster than Full table scan as we only load a subset of the records from the table, however, it slows down the insert, update and delete operations since it requries the computation of the index on every one of these operations for indexed columns.

- **Id Scans**: This can be lightning fast since records are stored with their id as file name. Therefore when querying by id we can directly access the right file and return the associated record. However it is only useful when the `id` column is provided in the query.

These tree ways for querying are referred to as strategies in the code and each have
their corresponding classes.

There is also a fourth strategy called MultiStrategy, which is used to represent the need of using more than one strategy for a query. A Multi strategy is therefore a mixture of an Index Scan and an Id Scan.

The `Query Planner` is a service that is responsible for analysing a query and determining which strategy is the best one for finding the records needed.

The Query planner works in the following way:
1. It traverses the Expression Tree of the query.
1. When finding a leaf node (A Term Node), analyses the term and finds the best strategy to use for it. For instance, when Id is used as the left operand, an Id scan will always be the fastest option for this term. Then, when an index is available, a index scan will always be the fastest option. and finally when no other options could be determined a full table scan is chosen.
1. When backtracking in the tree and arriving at Expression nodes, it will try to compare the two strategies used for the left and right branches, and determine the fastest one between the two, i.e. the one that would encompass the results of both strategies, but in the most efficient way. This comparison follows two truth tables that are dependent on the Expression operator. (More on that later)
1. At the end of the traversal, it should have found the most efficient strategy for all nodes in the tree. This strategy will then be available for use by the executor.

#### Truth table
##### AND expression operator table
TableScan AND TableScan => TableScan
TableScan AND IdScan => IdScan
TableScan AND IndexScan => IndexScan

IdScan AND TableScan => IdScan
IdScan AND IdScan => IdScan
IdScan AND IndexScan => IdScan 

IndexScan AND TableScan => IndexScan
IndexScan AND IdScan => IdScan
IndexScan AND IndexScan => IndexScan


##### OR expression operator table
TableScan OR TableScan => TableScan
TableScan OR IdScan => TableScan
TableScan OR IndexScan => TableScan

IdScan OR TableScan => TableScan
IdScan OR IdScan => IdScan
IdScan OR IndexScan => Both (MultiStrategy)

IndexScan OR TableScan => TableScan
IndexScan OR IdScan => Both (MultiStrategy)
IndexScan OR IndexScan -> IndexScan

MultiStrategy OR TableSCan => TableScan
MultiStrategy OR IdScan => Append(MultiStrategy) 
MultiStrategy OR IndexScan => Merge(MultiStrategy) 


### Execution
The executor simply applies the strategy, by loading the records associated in the strategy
and checking if the query matches them.

