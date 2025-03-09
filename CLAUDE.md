# BOSTARTER Project Configuration

## Database Setup
- Initialize DB: `cd db && ./init_demo.sh` (update MySQL password first)
- Run SQL scripts directly: `mysql -u root -p BOSTARTER < db/bostarter_init.sql`

## PHP Development
- Run local server: Use XAMPP or similar PHP server
- Copy files to web directory: `/Applications/XAMPP/xamppfiles/htdocs/bostarter/`

## Code Style Guidelines
- File names: snake_case
- Functions/variables: camelCase
- Database stored procedures: prefixed with `sp_`
- Error handling: Use try/catch with PDOException
- Database access: Always use sp_invoke() function

## Architecture
- Database-centric application using MySQL stored procedures
- Directory structure: actions/, components/, config/, functions/, public/
- Session-based authentication and flash messages
- Bootstrap 5 for UI components