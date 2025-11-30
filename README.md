# SmartTaskBoard

A modern, collaborative task management application built with Laravel. SmartTaskBoard provides teams with an intuitive Kanban-style board for organizing tasks, managing projects, and collaborating effectively.

## Features

### Task Management
- **Kanban Board Interface**: Drag-and-drop tasks between customizable columns (Todo, In Progress, Done)
- **Task CRUD Operations**: Create, read, update, and delete tasks with rich details
- **Task Search & Filtering**: Quickly find tasks by title, description, tags, or status
- **Task Editing**: Inline editing capabilities for quick updates
- **Priority Levels**: Assign priority levels to tasks for better organization
- **Due Dates**: Set deadlines and track task timelines
- **Attachments**: Upload and manage files associated with tasks

### Team Collaboration
- **Team Management**: Create and manage multiple teams
- **Role-Based Access Control**: Owner, Admin, and Member roles with different permissions
- **Team Invitations**: Invite members via email with secure token-based system
- **Member Management**: Add, remove, and update team member roles
- **Activity Tracking**: Monitor team activities and task changes

### Tags & Organization
- **Tag System**: Create and assign custom tags to tasks
- **Color-Coded Tags**: Visual organization with customizable tag colors
- **Tag Management**: Create, edit, and delete tags
- **Tag Filtering**: Filter tasks by single or multiple tags

### Comments & Communication
- **Task Comments**: Add comments to tasks for discussions
- **Real-time Updates**: Keep team members informed of changes
- **Comment Management**: Edit and delete your own comments
- **Mention System**: Tag team members in comments (future feature)

### User Management
- **Authentication**: Secure user registration and login
- **Profile Management**: Update user information and preferences
- **Password Reset**: Secure password recovery system
- **API Token Authentication**: Secure API access with Sanctum

### UI & UX Features
- **Dark Mode**: Full dark theme support with smooth transitions
- **Multi-Language**: Support for English and Russian languages
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Keyboard Shortcuts**: Fast navigation with hotkeys
- **Toast Notifications**: User-friendly notifications for all actions
- **Empty States**: Helpful placeholders when columns or boards are empty
- **Overdue Indicators**: Visual markers for tasks past due date

## Technology Stack

- **Backend**: Laravel 10.x
- **Database**: MySQL/PostgreSQL/SQLite
- **Authentication**: Laravel Sanctum
- **Testing**: PHPUnit
- **CI/CD**: GitHub Actions
- **Frontend**: Tailwind CSS + Alpine.js
- **Build Tool**: Vite
- **Drag & Drop**: SortableJS

## Requirements

- PHP >= 8.2
- Composer
- MySQL >= 5.7 or PostgreSQL >= 10
- Node.js >= 16.x
- NPM or Yarn

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/SmartTaskBoard.git
cd SmartTaskBoard
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smarttaskboard
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Database Setup

```bash
php artisan migrate --seed
```

### 5. Build Frontend Assets

```bash
npm run build
```

For development:

```bash
npm run dev
```

### 6. Start the Application

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Docker Installation (Alternative)

### 1. Build and Start Containers

```bash
docker-compose up -d
```

### 2. Install Dependencies

```bash
docker-compose exec app composer install
docker-compose exec app npm install
```

### 3. Setup Application

```bash
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
docker-compose exec app npm run build
```

The application will be available at `http://localhost:8000`

## Usage Guide

### Creating Your First Team

1. Register a new account or login
2. Navigate to Teams section
3. Click "Create Team"
4. Enter team name and description
5. You'll be automatically assigned as the team owner

### Adding Team Members

1. Go to your team settings
2. Click "Invite Member"
3. Enter the member's email address and select their role
4. An invitation will be sent to their email
5. They can accept the invitation to join your team

### Creating Tasks

1. Select a team from the dashboard
2. Click "Add Task" in any column
3. Fill in task details:
   - Title (required)
   - Description
   - Priority level
   - Due date
   - Assigned member
   - Tags
4. Click "Create Task"

### Moving Tasks

- Drag and drop tasks between columns to update their status
- Tasks automatically update their status based on the column
- All team members see updates in real-time

### Using Tags

1. Go to Tag Management
2. Create new tags with custom names and colors
3. Assign tags to tasks during creation or editing
4. Filter tasks by clicking on tags in the filter panel

### Search and Filter

- **Search Bar**: Search tasks by title or description
- **Tag Filter**: Filter by one or multiple tags
- **Status Filter**: Show tasks from specific columns
- **Clear Filters**: Reset all filters to show all tasks

### Adding Comments

1. Open a task detail view
2. Scroll to the comments section
3. Type your comment in the text area
4. Click "Post Comment"
5. Edit or delete your own comments as needed

### Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| **N** | Create new task |
| **B** | Open boards menu |
| **T** | Manage tags |
| **D** | Toggle dark mode |
| **L** | Switch language |
| **/** | Focus search bar |
| **ESC** | Close any modal |

## API Documentation

### Authentication Endpoints

#### Register User
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

### Task Endpoints

#### List Tasks
```http
GET /api/tasks
Authorization: Bearer {token}
```

#### Create Task
```http
POST /api/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Task Title",
  "description": "Task description",
  "status": "todo",
  "priority": "high",
  "due_date": "2024-12-31",
  "team_id": 1
}
```

#### Update Task
```http
PUT /api/tasks/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Updated Title",
  "status": "in_progress"
}
```

#### Delete Task
```http
DELETE /api/tasks/{id}
Authorization: Bearer {token}
```

### Team Endpoints

#### Create Team
```http
POST /api/teams
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Team Name",
  "description": "Team description"
}
```

#### Invite Member
```http
POST /api/teams/{id}/invite
Authorization: Bearer {token}
Content-Type: application/json

{
  "email": "member@example.com",
  "role": "member"
}
```

#### Remove Member
```http
DELETE /api/teams/{teamId}/members/{userId}
Authorization: Bearer {token}
```

### Comment Endpoints

#### Create Comment
```http
POST /api/tasks/{taskId}/comments
Authorization: Bearer {token}
Content-Type: application/json

{
  "content": "Comment text"
}
```

#### Delete Comment
```http
DELETE /api/comments/{id}
Authorization: Bearer {token}
```

## Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
php artisan test --testsuite=Feature
```

### Run with Coverage

```bash
php artisan test --coverage
```

### Run Specific Test File

```bash
php artisan test tests/Feature/TaskTest.php
```

## Development

### Code Style

This project follows PSR-12 coding standards. Format your code using:

```bash
./vendor/bin/pint
```

### Database Seeding

Seed the database with sample data:

```bash
php artisan db:seed
```

### Cache Management

Clear application cache:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Contributing

We welcome contributions to SmartTaskBoard! Please follow these guidelines:

### Getting Started

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Make your changes
4. Write or update tests for your changes
5. Ensure all tests pass (`php artisan test`)
6. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
7. Push to the branch (`git push origin feature/AmazingFeature`)
8. Open a Pull Request

### Pull Request Guidelines

- **Clear Description**: Explain what your PR does and why
- **Tests Required**: All new features must include tests
- **Code Style**: Follow PSR-12 standards (run `./vendor/bin/pint`)
- **Documentation**: Update README.md if adding new features
- **Single Responsibility**: One feature or fix per PR
- **Commit Messages**: Use clear, descriptive commit messages

### Coding Standards

- Follow Laravel best practices
- Write meaningful variable and function names
- Add comments for complex logic
- Keep functions small and focused
- Use type hints for parameters and return types
- Validate all user inputs
- Handle errors gracefully

### Reporting Bugs

When reporting bugs, please include:

- Clear bug description
- Steps to reproduce
- Expected behavior
- Actual behavior
- PHP and Laravel version
- Error messages or logs

### Feature Requests

We love new ideas! When requesting features:

- Explain the use case
- Describe the desired behavior
- Consider if it fits the project scope
- Be open to discussion and feedback

## Security

If you discover any security vulnerabilities, please email security@smarttaskboard.com instead of using the issue tracker. All security vulnerabilities will be promptly addressed.

### Security Best Practices

- Always use HTTPS in production
- Keep dependencies updated
- Use strong passwords
- Enable two-factor authentication (coming soon)
- Regularly backup your database
- Monitor application logs for suspicious activity

## License

SmartTaskBoard is open-source software licensed under the [MIT License](LICENSE).

```
MIT License

Copyright (c) 2024 SmartTaskBoard

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## Credits

Developed and maintained by the SmartTaskBoard team.

### Acknowledgments

- Laravel Framework
- Tailwind CSS
- Alpine.js
- SortableJS
- All open-source contributors
- Community feedback and support

## Support

- **Documentation**: [https://docs.smarttaskboard.com](https://docs.smarttaskboard.com)
- **Issues**: [GitHub Issues](https://github.com/yourusername/SmartTaskBoard/issues)
- **Discussions**: [GitHub Discussions](https://github.com/yourusername/SmartTaskBoard/discussions)
- **Email**: support@smarttaskboard.com

## Roadmap

- [ ] Real-time notifications
- [ ] WebSocket support for live updates
- [ ] File attachments
- [ ] Task templates
- [ ] Time tracking
- [ ] Calendar view
- [ ] Mobile application
- [ ] Third-party integrations (Slack, GitHub, etc.)
- [ ] Advanced reporting and analytics
- [ ] Custom fields
- [ ] Automation rules
- [ ] Two-factor authentication

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for detailed version history.

---

Made with care by the SmartTaskBoard team
