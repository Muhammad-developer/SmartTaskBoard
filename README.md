# Smart Task Board

A beautiful, modern task management application built with Laravel, Tailwind CSS, and Alpine.js. Features drag-and-drop functionality, smooth animations, and a stunning UI.

## Features

- **Drag & Drop**: Intuitive drag-and-drop interface powered by SortableJS
- **Beautiful UI**: Modern, clean design with Tailwind CSS and smooth animations
- **Task Management**: Create, update, and delete tasks with priorities and due dates
- **Tags System**: Organize tasks with custom colored tags
- **Multiple Columns**: Customizable board columns (To Do, In Progress, Review, Done)
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Real-time Ready**: Built with Laravel Broadcasting support for real-time updates

## Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Tailwind CSS 3 + Alpine.js 3
- **Build Tool**: Vite 5
- **Drag & Drop**: SortableJS
- **Database**: SQLite (default, can be changed to MySQL/PostgreSQL)

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18 or higher
- npm

### Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd SmartTaskBoard
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run migrations**
   ```bash
   php artisan migrate:fresh
   ```

6. **Start development servers**

   In one terminal:
   ```bash
   npm run dev
   ```

   In another terminal:
   ```bash
   php artisan serve
   ```

7. **Open your browser**

   Navigate to `http://localhost:8000`

## Usage

### Creating Tasks

1. Click the "New Task" button in the header
2. Fill in the task details:
   - Select a column
   - Enter task title (required)
   - Add description (optional)
   - Set priority level
   - Set due date (optional)
3. Click "Create Task"

### Moving Tasks

Simply drag and drop tasks between columns! The position will be saved automatically.

### Deleting Tasks

Click the trash icon on any task card to delete it.

## Project Structure

```
SmartTaskBoard/
├── app/
│   ├── Http/Controllers/
│   │   ├── BoardController.php    # Board management
│   │   ├── TaskController.php     # Task CRUD and drag-drop
│   │   └── TagController.php      # Tag management
│   └── Models/
│       ├── Board.php
│       ├── Column.php
│       ├── Task.php
│       └── Tag.php
├── database/
│   └── migrations/                # Database schema
├── resources/
│   ├── css/
│   │   └── app.css               # Tailwind styles
│   ├── js/
│   │   ├── app.js                # Alpine.js setup
│   │   └── bootstrap.js          # Laravel Echo config
│   └── views/
│       ├── layout.blade.php      # Main layout
│       └── board.blade.php       # Task board view
├── routes/
│   └── web.php                   # Application routes
└── tailwind.config.js            # Tailwind configuration
```

## Customization

### Adding New Columns

You can add new columns by modifying the `createDefaultBoard()` method in `BoardController.php`:

```php
$columns = [
    ['name' => 'To Do', 'color' => '#ef4444', 'position' => 0],
    ['name' => 'In Progress', 'color' => '#f59e0b', 'position' => 1],
    ['name' => 'Your New Column', 'color' => '#3b82f6', 'position' => 2],
    // ...
];
```

### Changing Colors

Edit `tailwind.config.js` to customize the color scheme:

```javascript
theme: {
  extend: {
    colors: {
      primary: {
        // Your custom colors
      },
    },
  },
}
```

## API Endpoints

- `GET /` - Display main board
- `POST /tasks` - Create new task
- `PUT /tasks/{task}` - Update task
- `DELETE /tasks/{task}` - Delete task
- `POST /api/tasks/{task}/move` - Move task to different column/position
- `GET /api/boards/{board}/data` - Get board data with all tasks

## Performance

- Optimized database queries with eager loading
- Smooth CSS transitions using Tailwind utilities
- Efficient drag-and-drop with SortableJS
- Minimal JavaScript footprint with Alpine.js

## Future Enhancements

- [ ] Real-time collaboration with Pusher/Laravel Echo
- [ ] Task search and filtering
- [ ] User authentication
- [ ] Task assignments
- [ ] File attachments
- [ ] Activity timeline
- [ ] Dark mode
- [ ] Export to PDF/CSV

## Contributing

Feel free to submit issues and enhancement requests!

## License

This project is open-sourced software licensed under the MIT license.

## Credits

Built with:
- [Laravel](https://laravel.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
- [SortableJS](https://sortablejs.github.io/Sortable/)

---

Made with ❤️ for amazing task management
