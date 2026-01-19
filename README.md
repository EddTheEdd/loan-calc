# Loan calculator

## Setup

- Clone repository
- Start containers:
  - docker compose up -d
- Install PHP dependencies:
  - docker compose exec app composer install
- Open:
  - http://localhost:8000/

## Testing

- docker compose exec -T app composer test

PHPUnit tests are included to verify functionality.:

- Correct monthly payment calculation for the required annuity loan cases (Case 1 / Case 2).

- Validation rules and structured error messages.

- Structure and consistency of amortization schedule.

Tests are provided for annuity only as this is just a practice exercise.

## Task estimation vs time spent

| Area      | Estimated (h) | Actual (h) | Difference (h) |
| --------- | ------------- | ---------- | -------------- |
| Setup     | 1             | 0.5        | -0.5           |
| Frontend  | 2             | 1.5        | -0.5           |
| Backend   | 3             | 2          | -1.0           |
| Styling   | 0.5           | 1          | +0.5           |
| Tests     | 1             | 1          | 0.0            |
| **Total** | **7.5**       | **6**     | **1.5**        |


## Task report

- Original plan was to approach this problem from something that I know well - setting up Laravel with a React frontend, but after delving deeper into the option of a plain php solution I quickly understood that even if new to me personally, it will probably be much quicker to do it this way.

- The original boilerplate I used was put together using the help of an AI tool as the optimal structure of a plain PHP project was new to me.

- Functionallity, validation extending and styling was done by myself.

## Design choice reasoning

- |Tech stack| While a Laravel + React project would make the project much more familiar, the project is so small and functionally trivial, that such an approach isn't necessary so I opted for a plain PHP approach.

- |Validation| Originally the validation was in the frontend using the "min" parameter, but I decided to demonstrate backend validation example as the focus is more on the backend. This required returning the key of the failed input field to properly highlight the errored input field.

- |Annuity loan| Annuity schedule doesn't distribute the rounding error over many months, but rather tells the user that he will have to pay the rounding error in the last month. As far as I understood, this is an okay approach.

