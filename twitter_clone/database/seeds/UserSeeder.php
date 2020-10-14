<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        factory(User::class, 100)->create()
            ->each(function (User $user) {
                $followees = User::query()
                    ->whereNotIn('id', [$user->id])
                    ->inRandomOrder()
                    ->take(random_int(0, 10))
                    ->get();
                $user->followees()->sync($followees->pluck('id'));

                $block_user = User::query()
                    ->whereNotIn('id', [$user->id])
                    ->inRandomOrder()
                    ->firstOr();
                $user->blocking()->attach($block_user->id);

                $mute_user = User::query()
                    ->whereNotIn('id', [$user->id])
                    ->inRandomOrder()
                    ->firstOr();
                $user->muting()->attach($mute_user->id);

                $user->tweets()->saveMany(
                    factory(App\Tweet::class, random_int(0, 1000))->make()
                );
            });
    }
}
