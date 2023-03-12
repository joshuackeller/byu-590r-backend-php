<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $books = [
            [
                'name' => "Boys in the Boat",
                'description' => "A book about the 1936  United States olympic rowing team",
                'book_cover_picture' => 'images/download-6.jpg',      
                'inventory_total_qty' => 1,
                'checked_qty' => 0
            ],
            [
                'name' => "Shoe Dog",
                'description' => "The story of the founding of Nike",
                'book_cover_picture' => 'images/download.png',       
                'inventory_total_qty' => 1,
                'checked_qty' => 0
            ],
            [
                'name' => "How Will You Measure Your Life",
                'description' => "Clayton Christiansen breaks down the most important aspects of life, how we can control them, and how we should measure them",
                'book_cover_picture' => 'images/download-7.jpg',       
                'inventory_total_qty' => 1,
                'checked_qty' => 0
            ],
            [
                'name' => "Unbroken",
                'description' => "The story of Louis Zamperini's life and he's experiences in WWII",
                'book_cover_picture' => 'images/download-8.jpg',       
                'inventory_total_qty' => 1,
                'checked_qty' => 0
            ],
            [
                'name' => "Steve Jobs",
                'description' => "Walter Isaacson's biography of Steve Jobs",
                'book_cover_picture' => 'images/download-9.jpg',       
                'inventory_total_qty' => 1,
                'checked_qty' => 0
            ],
         ];

         Book::insert($books); 
    }
}
