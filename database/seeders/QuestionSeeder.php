<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Beginner (10 أسئلة)
        Question::create([
            'field_id' => 1,
            'title' => 'Two Sum',
            'description' => 'Find two numbers that add to target',
            'difficulty' => 'beginner'
        ]);

        Question::create([
            'field_id' => 1,
            'title' => 'Binary Search',
            'description' => 'Search element in sorted array',
            'difficulty' => 'beginner'
        ]);

        Question::create([
            'field_id' => 1,
            'title' => 'Merge Sorted Arrays',
            'description' => 'Merge two sorted arrays into one',
            'difficulty' => 'beginner'
        ]);

        Question::create([
            'field_id' => 1,
            'title' => 'Find Maximum Number',
            'description' => 'Find the maximum number in an array',
            'difficulty' => 'beginner'
        ]);

        Question::create([
            'field_id' => 1,
            'title' => 'Palindrome Check',
            'description' => 'Check if a string is a palindrome',
            'difficulty' => 'beginner'
        ]);

        Question::create([
            'field_id' => 1,
            'title' => 'Factorial',
            'description' => 'Calculate factorial of a number',
            'difficulty' => 'beginner'
        ]);

        Question::create([
            'field_id' => 1,
            'title' => 'Fibonacci Sequence',
            'description' => 'Generate first N Fibonacci numbers',
            'difficulty' => 'beginner'
        ]);

        Question::create([
            'field_id' => 1,
            'title' => 'Count Vowels',
            'description' => 'Count number of vowels in a string',
            'difficulty' => 'beginner'
        ]);

        Question::create([
            'field_id' => 1,
            'title' => 'Reverse String',
            'description' => 'Reverse a given string',
            'difficulty' => 'beginner'
        ]);

        Question::create([
            'field_id' => 1,
            'title' => 'Find Minimum Number',
            'description' => 'Find the minimum number in an array',
            'difficulty' => 'beginner'
        ]);

        // Intermediate (10 أسئلة)
        Question::create([
            'field_id' => 2,
            'title' => 'Reverse Linked List',
            'description' => 'Reverse a singly linked list',
            'difficulty' => 'intermediate'
        ]);

        Question::create([
            'field_id' => 2,
            'title' => 'Detect Cycle in Linked List',
            'description' => 'Check if linked list has a cycle',
            'difficulty' => 'intermediate'
        ]);

        Question::create([
            'field_id' => 2,
            'title' => 'Merge Two Sorted Lists',
            'description' => 'Merge two sorted linked lists',
            'difficulty' => 'intermediate'
        ]);

        Question::create([
            'field_id' => 2,
            'title' => 'Remove Duplicates from Sorted List',
            'description' => 'Remove duplicates from linked list',
            'difficulty' => 'intermediate'
        ]);

        Question::create([
            'field_id' => 2,
            'title' => 'Binary Tree Inorder Traversal',
            'description' => 'Traverse a binary tree inorder',
            'difficulty' => 'intermediate'
        ]);

        Question::create([
            'field_id' => 2,
            'title' => 'Binary Tree Level Order',
            'description' => 'Print level order traversal of binary tree',
            'difficulty' => 'intermediate'
        ]);

        Question::create([
            'field_id' => 2,
            'title' => 'Implement Queue using Stacks',
            'description' => 'Use two stacks to implement a queue',
            'difficulty' => 'intermediate'
        ]);

        Question::create([
            'field_id' => 2,
            'title' => 'Valid Parentheses',
            'description' => 'Check if parentheses are valid',
            'difficulty' => 'intermediate'
        ]);

        Question::create([
            'field_id' => 2,
            'title' => 'Sort Colors',
            'description' => 'Sort array with 0,1,2 in-place',
            'difficulty' => 'intermediate'
        ]);

        Question::create([
            'field_id' => 2,
            'title' => 'Climbing Stairs',
            'description' => 'Count ways to climb stairs (dynamic programming)',
            'difficulty' => 'intermediate'
        ]);

        // Advanced (10 أسئلة)
        Question::create([
            'field_id' => 3,
            'title' => 'Shortest Path',
            'description' => 'Find shortest path in graph',
            'difficulty' => 'advanced'
        ]);

        Question::create([
            'field_id' => 3,
            'title' => 'Dijkstra Algorithm',
            'description' => 'Find shortest paths from source in weighted graph',
            'difficulty' => 'advanced'
        ]);

        Question::create([
            'field_id' => 3,
            'title' => 'Topological Sort',
            'description' => 'Sort directed acyclic graph',
            'difficulty' => 'advanced'
        ]);

        Question::create([
            'field_id' => 3,
            'title' => 'Minimum Spanning Tree',
            'description' => 'Find MST using Kruskal or Prim',
            'difficulty' => 'advanced'
        ]);

        Question::create([
            'field_id' => 3,
            'title' => 'Word Ladder',
            'description' => 'Transform word using minimal steps',
            'difficulty' => 'advanced'
        ]);

        Question::create([
            'field_id' => 3,
            'title' => 'Longest Increasing Subsequence',
            'description' => 'Find length of LIS in array',
            'difficulty' => 'advanced'
        ]);

        Question::create([
            'field_id' => 3,
            'title' => 'Knapsack Problem',
            'description' => '0/1 knapsack using DP',
            'difficulty' => 'advanced'
        ]);

        Question::create([
            'field_id' => 3,
            'title' => 'Graph Cycle Detection',
            'description' => 'Detect cycle in directed graph',
            'difficulty' => 'advanced'
        ]);

        Question::create([
            'field_id' => 3,
            'title' => 'Connected Components',
            'description' => 'Count connected components in graph',
            'difficulty' => 'advanced'
        ]);

        Question::create([
            'field_id' => 3,
            'title' => 'Maximum Flow',
            'description' => 'Find maximum flow in flow network',
            'difficulty' => 'advanced'
        ]);
    }
}
