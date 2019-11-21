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
    - left: TreeNode
            - left: genre == 'adventure' => criterion (TreeNode)
            - operator: AND => (TreeOperator)
            - price == '5.00' => criterion (TreeNode)
    - operator: OR
    - right: TreeNode
            - left: genre == 'crime' => criterion (TreeNode)
            - operator: AND => (TreeOperator)
            - price == '10.00' => criterion (TreeNode)



$qb->where(
        $qb->where('genre', Operator::EQUAL(), 5)
            ->andWhere('genre', Operator::EQUAL(), crime')
    )
    ->orWhere(
        $qb->where('genre', Operator::EQUAL(), 5)
        ->andWhere('genre', Operator::EQUAL(), crime')
    )




