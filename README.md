# authorizer-challenge

### How to run it
php src/Authorizer.php operations

### Prerequisites
- PHP 8
- Composer 2.1.3

### Possible improvements and design decisions
- Regarding violations, I decided to validate all requisites to give the user a whole list of violations to be able to fix all of them once. I think this is more user friendly, but if the applications grows, it's necessary to evaluate performance issues.
- I assumed that each transaction is sorted chronologically
- Add more unit tests