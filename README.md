# FieldLog

A personal multimedia journaling application built with Symfony 7.3 that allows you to create and manage different types of entries in your personal log.

## About

FieldLog is a web application designed to help you organize and store your thoughts, bookmarks, and files in a chronological journal. Whether you want to save text notes, bookmark important links, or store documents and images, FieldLog provides a unified interface to manage all your content.

## Features

### Three Entry Types

* **Text Entries**: Write and save personal notes, thoughts, and reflections
* **Link Entries**: Bookmark URLs with descriptive titles for easy reference
* **File Entries**: Upload and store files including images, documents, and other media

### Core Functionality

* **Timestamped Entries**: All entries are automatically timestamped with publication date
* **Paginated Lists**: Browse your entries with pagination (5 entries per page by default)
* **Type Filtering**: Organize and view entries by type
* **Rich Metadata**: Each entry includes title (up to 180 characters), type, and relevant content

## Tech Stack

* **Backend**: Symfony 7.3, PHP 8.2+, Doctrine ORM
* **Frontend**: Twig templates, Stimulus controllers, TailwindCSS
* **Components**: Symfony UX (Live Components, Twig Components, Icons)
* **Infrastructure**: Docker with FrankenPHP and Caddy
* **Storage**: Doctrine for database, Flysystem for file management

## Getting Started

### Prerequisites

* [Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
* [Castor](https://castor.jolicode.com/) - PHP task runner for managing the project

### Installation

1. Clone the repository
2. Build and start the infrastructure:
   ```bash
   castor build
   castor start
   ```
3. Install dependencies:
   ```bash
   castor install
   ```
4. Open `https://localhost` in your web browser and accept the auto-generated TLS certificate

### Available Commands

#### Infrastructure Management

* `castor build` - Build Docker images
* `castor start` - Start containers (also compiles frontend)
* `castor stop` - Stop containers
* `castor restart` - Restart containers
* `castor destroy` - Remove all containers, volumes, and networks

#### Development

* `castor install` - Install Composer dependencies
* `castor update` - Update dependencies and run migrations
* `castor frontend` - Compile frontend assets
* `castor frontend --watch` - Watch mode for frontend development
* `castor console <command>` - Run Symfony console commands

#### Code Quality

* `castor ecs` - Check code style with Easy Coding Standard
* `castor ecs --fix` - Automatically fix code style issues
* `castor rector` - Analyze code with Rector
* `castor rector --fix` - Apply automatic refactorings
* `castor phpstan` - Run static analysis with PHPStan
* `castor lint` - Check PHP syntax
* `castor deptrac` - Verify architecture dependencies

#### Testing

* `castor phpunit` - Run PHPUnit tests with coverage
* `castor infect` - Run mutation testing with Infection

#### Complete Workflow

* `castor prepare_pr` - Prepare code for pull request (fix style, run static analysis)

All QA tools run in an isolated Docker container (`ghcr.io/spomky-labs/phpqa:8.4`) to ensure consistent analysis across environments.

## Project Structure

```
src/
├── Controller/     # Application controllers
├── Entity/         # Doctrine entities
└── Repository/     # Database repositories

templates/          # Twig templates
assets/            # Frontend assets (JS, CSS)
.ci-tools/         # Configuration for QA tools
```

## License

This project is proprietary software.
