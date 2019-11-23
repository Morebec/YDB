# Find 2$ books
FIND 
    WHERE price == 2.00

Root
    - left: price == 2.00 
    - operator: null
    - right: null

# Find find 5$ adventure books, or 10$ crime books
FIND 
        WHERE (genre == 'adventure' AND price == '5.00') 
    OR 
        WHERE (genre == 'crime' AND price == '10.00')
    
Root
    - left: ExpressionNode
            - left: genre == 'adventure' => criterion (ExpressionNode)
            - operator: AND => (ExpressionOperator)
            - price == '5.00' => criterion (ExpressionNode)
    - operator: OR
    - right: ExpressionNode
            - left: genre == 'crime' => criterion (ExpressionNode)
            - operator: AND => (ExpressionOperator)
            - price == '10.00' => criterion (ExpressionNode)



$qb->where(
        $qb->where('genre', Operator::EQUAL(), 5)
            ->andWhere('genre', Operator::EQUAL(), crime')
    )
    ->orWhere(
        $qb->where('genre', Operator::EQUAL(), 5)
        ->andWhere('genre', Operator::EQUAL(), crime')
    )




