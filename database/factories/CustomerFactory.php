<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'middlename' => $this->faker->firstName(),
            'notifyemails' => $this->faker->boolean(),
            'billingemail' => $this->faker->safeEmail(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'zip' => $this->faker->postcode(),
            'textphones' => $this->faker->phoneNumber(),
            'status' => $this->faker->randomElement([1, 2, 3]),
            'e_billing' => $this->faker->boolean(),
            'yearly_billing' => $this->faker->boolean(),
            'balance' => $this->faker->numberBetween(0, 100000), // assuming balance is stored in cents based on migrations using integer
            'adjustment' => $this->faker->numberBetween(0, 10000),
            'qid' => $this->faker->unique()->numberBetween(1000, 9999),
        ];
    }
}
