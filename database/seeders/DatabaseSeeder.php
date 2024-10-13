<?php

namespace Database\Seeders;

use App\Models\Interest;
use App\Models\Interests;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		// User::factory(10)->create();

		User::factory()->create([
			'name' => 'John Doe',
			'email' => 'admin@admin.com',
			'password' => bcrypt('123456'),
		]);

		//seed 10 interests
		$life_advice_topics = [
			"Time Management",
			"Mental Health",
			"Financial Planning",
			"Healthy Relationships",
			"Conflict Resolution",
			"Work-Life Balance",
			"Overcoming Procrastination",
			"Positive Thinking",
			"Self-Discipline",
			"Embracing Failure",
			"Career Growth",
			"Physical Fitness",
			"Healthy Eating",
			"Goal Setting",
			"Building Confidence",
			"Mindful Living",
			"Effective Communication",
			"Stress Management",
			"Learning to Say No",
			"Continuous Learning",
			"Emotional Intelligence",
			"Dealing with Anxiety",
			"Minimalism",
			"Public Speaking",
			"Leadership Skills",
			"Overcoming Perfectionism",
			"Digital Detox",
			"Charity and Giving Back",
			"Travel and Exploration",
			"Finding Purpose",
			"leo messi"
		];
		//loop through the interests array and create a new interest for each item
		foreach ($life_advice_topics as $advice_topic) {
			Interest::query()->create([
				'name' => $advice_topic,
			]);
		}
	}
}
