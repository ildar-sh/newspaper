<?php
return [
    'managePost' => [
        'type' => 2,
        'description' => 'Manage posts',
    ],
    'viewPost' => [
        'type' => 2,
        'description' => 'View post',
    ],
    'createPost' => [
        'type' => 2,
        'description' => 'Create a post',
    ],
    'updatePost' => [
        'type' => 2,
        'description' => 'Update post',
    ],
    'deletePost' => [
        'type' => 2,
        'description' => 'Delete post',
    ],
    'updateOwnPost' => [
        'type' => 2,
        'description' => 'Update own post',
        'ruleName' => 'isAuthor',
        'children' => [
            'updatePost',
        ],
    ],
    'deleteOwnPost' => [
        'type' => 2,
        'description' => 'Delete own post',
        'ruleName' => 'isAuthor',
        'children' => [
            'deletePost',
        ],
    ],
    'user' => [
        'type' => 1,
        'children' => [
            'viewPost',
        ],
    ],
    'manager' => [
        'type' => 1,
        'children' => [
            'managePost',
            'createPost',
            'user',
            'updateOwnPost',
            'deleteOwnPost',
        ],
    ],
    'admin' => [
        'type' => 1,
        'children' => [
            'updatePost',
            'deletePost',
            'manager',
        ],
    ],
    'default_role' => [
        'type' => 1,
    ],
];
