<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PostSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('email', 'admin@sciencesport.org')->first();
        if (!$admin) return;

        $mainPost = Post::create([
            'user_id' => $admin->id,
            'title' => 'Golf Classic',
            'subtitle' => 'Empowering Students Through Sports',
            'event_date' => '2025-11-10',
            'location' => 'Calabasas Country Club',
            'overview' => 'When you tee it up at the Science of Sport Golf Classic, you drive our organization forward. With your support, more students in more schools across LA County will experience the transformational learning experiences we provide. Whether you are slicing into the fairway bunker, hooking onto the cart path or bombing it down the middle, your day on the course will be a beautiful one as you help Science of Sport thrive. Come on out!',
        ]);

        $packages = [
            [
                'type' => 'sponsorship',
                'name' => 'Title Sponsor',
                'price' => 15000.00,
                'capacity' => 12,
                'description' => 'Wear the Green Jacket for the day. Includes Prominent placement on all digital and print communications, VIP Table at 19th Hole, Signature Drink, Three Foursomes, Branded Item in Gift Bags, Dedicated Hole for Activation, and Recognition Throughout the Event.'
            ],
            [
                'type' => 'sponsorship',
                'name' => 'Champion',
                'price' => 8500.00,
                'capacity' => 8,
                'description' => 'Drive the ambiance of the day from warm up to sun down.'
            ],
            [
                'type' => 'sponsorship',
                'name' => 'All Star',
                'price' => 5000.00,
                'capacity' => 4,
                'description' => 'Craft the course environment with 1 of 18 tailored hole activations and on-course experiences.'
            ],
            [
                'type' => 'sponsorship',
                'name' => 'Mvp',
                'price' => 3000.00,
                'capacity' => 4,
                'description' => 'Choose 1 of the 7 opportunities to spotlight your brand as an off-course experience sponsor.'
            ],
            [
                'type' => 'golf_only',
                'name' => 'Foursome',
                'price' => 1800.00,
                'capacity' => 4,
                'description' => null
            ],
            [
                'type' => 'golf_only',
                'name' => 'Single',
                'price' => 450.00,
                'capacity' => 1,
                'description' => null
            ],
            [
                'type' => 'golf_only',
                'name' => '19th Hole Attendee',
                'price' => 45.00,
                'capacity' => 1,
                'description' => null
            ]
        ];

        $mainPost->packages()->createMany($packages);

        // Just to create more posts :v
        for ($i = 1; $i <= 15; $i++) {
            Post::create([
                'user_id' => $admin->id,
                'title' => 'Science of Sport Invitational Vol. ' . $i,
                'subtitle' => 'Annual Fundraising Event',
                'event_date' => Carbon::parse('2025-11-10')->addMonths($i)->format('Y-m-d'),
                'location' => ['Los Angeles, CA', 'Miami, FL', 'Dallas, TX', 'Phoenix, AZ'][array_rand(['Los Angeles, CA', 'Miami, FL', 'Dallas, TX', 'Phoenix, AZ'])],
                'overview' => 'Join us for our annual fundraising event supporting STEM education through sports. Your participation helps us reach more students across the nation.',
            ]);
        }
    }
}
