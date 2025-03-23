# BOSTARTER

**University project for my Database course.**

BOSTARTER is a crowdfunding platform that allows users to create, finance, and participate in hardware and software projects. The platform facilitates project creation, funding, and collaboration through a comprehensive web interface.

## Architecture
---
BOSTARTER follows a three-tier architecture:

- **Frontend**: PHP 8.2 with Apache web server
- **Primary Database**: MySQL 8.0 (stores application data)
- **Secondary Database**: MongoDB (logs system events)

The entire application is containerized using Docker to ensure consistent deployment across environments.

## Prerequisites
---
- Docker and Docker Compose
- Available ports:
    - 8080 (Web interface)
    - 3307 (MySQL connection)
    - 27017 (MongoDB connection)

## Installation
---
1. Clone this repository
2. Navigate to the project directory
3. Make the initialization script executable, and run it:
```bash
chmod +x init.sh
./init.sh
```
4. Wait for the system to display:
```
=== BOSTARTER INIZIALIZZATO. PIATTAFORMA PRONTA! ===
```
5. Access the platform at http://localhost:8080

## Project Structure
---
```
bostarter/
├── actions/       # Operation handlers (db interactions)
├── components/    # Reusable UI components
├── config/        # Configuration files
├── functions/     # Utility functions
├── public/        # Client-facing pages
│   └── libs/      # Frontend libraries
├── db/            # Database initialization scripts
├── init.sh        # Initialization script
├── Dockerfile     # Docker configuration
└── docker-compose.yml # Docker Compose configuration
```
### Key Files
- **`config/config.php`**: Database connection and global configuration
- **`db/01-bostarter_init.sql`**: Database schema, stored procedures, triggers, and events
- **`functions/EventPipeline.php`**: Core pipeline for handling operations and logging
- **`functions/sp_invoke.php`**: Interface for invoking stored procedures

## Core Features
---
### User Management
- **Registration**: Create accounts with optional creator/admin roles
- **Authentication**: Login with email/password (admin requires security code)
- **Curriculum**: Manage personal skills and competencies

### Project Management
- **Project Creation**: Create hardware or software projects
- **Project Details**: Add descriptions, photos, budgets, deadlines
- **Hardware Projects**: Manage components with quantities and prices
- **Software Projects**: Define required profiles with skill requirements

### Financing
- **Project Funding**: Finance projects with customizable amounts
- **Rewards**: Receive rewards based on funding amount
- **Funding History**: Track personal contributions

### Participation (Software Projects)
- **Applications**: Apply for roles in software projects
- **Skill Matching**: System ensures applicants meet skill requirements
- **Application Management**: Creators can accept/reject applications

### Administration
- **Global Skills**: Administrators manage the platform-wide skill list
- **System Logs**: Monitor all platform activities (MongoDB)

## Database Schema
---
The database uses 16 tables to represent entities and relationships within the system:

### Primary Entities
- **UTENTE**: All users (email, password, personal details)
- **ADMIN**: Administrator-specific data
- **CREATORE**: Project creator-specific data
- **PROGETTO**: Generic project information
- **PROGETTO_SOFTWARE/PROGETTO_HARDWARE**: Project type specializations

### Project Components
- **REWARD**: Rewards offered for project funding
- **FOTO**: Project images
- **COMPONENTE**: Hardware project components
- **PROFILO**: Software project required profiles
- **SKILL**: Global skill list

### Interactions
- **FINANZIAMENTO**: Project funding records
- **COMMENTO**: User comments on projects
- **PARTECIPANTE**: User applications to software projects
- **SKILL_CURRICULUM**: User skills and competency levels
- **SKILL_PROFILO**: Skills required for project profiles

The database implements business rules through a combination of:
- Table constraints
- Stored procedures with validation logic
- Triggers for maintaining data consistency
- Views for efficient statistics reporting

For detailed usage instructions and examples, refer to the project documentation.