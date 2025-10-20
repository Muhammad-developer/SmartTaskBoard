# Smart Task Board

A beautiful, modern task management application built with Laravel, Tailwind CSS, and Alpine.js. Features drag-and-drop functionality, dark mode, multi-language support, and a stunning UI.

## Features

### Core Features
- **Drag & Drop**: Intuitive drag-and-drop interface powered by SortableJS
- **Beautiful UI**: Modern, clean design with Tailwind CSS and smooth animations
- **Task Management**: Create, update, and delete tasks with priorities and due dates
- **Tags System**: Organize tasks with custom colored tags
- **Multiple Boards**: Create and manage multiple task boards
- **Dynamic Columns**: Add, edit, and delete columns with custom names and colors
- **Responsive Design**: Works perfectly on desktop and mobile devices

### Advanced Features
- **🌙 Dark Mode**: Full dark theme support with smooth transitions
- **🌍 Multi-Language**: Support for English and Russian languages
- **💾 Persistent Preferences**: Theme and language preferences saved in localStorage
- **🎨 Custom Column Colors**: Choose custom colors for each column
- **⚡ Real-time Ready**: Built with Laravel Broadcasting support for real-time updates

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

### Managing Boards

1. **View All Boards**: Click the "Boards" button in the header
2. **Create New Board**:
   - Click "Boards" button
   - Click "New Board" in the modal
   - Enter board name and description
   - Click "Create"
3. **Switch Boards**: Click on any board name in the boards list

### Managing Columns

1. **Add Column**: Click the "Add Column" button at the end of the board
2. **Edit Column**: Click the edit icon in the column header
3. **Delete Column**: Click the trash icon in the column header
4. **Customize Colors**: Choose custom colors when creating or editing columns

### Creating Tasks

1. Click the "New Task" button in the header
2. Fill in the task details:
   - Select a column
   - Enter task title (required)
   - Add description (optional)
   - Set priority level (Low, Medium, High, Urgent)
   - Set due date (optional)
3. Click "Create Task"

### Moving Tasks

Simply drag and drop tasks between columns! The position will be saved automatically.

### Deleting Tasks

Click the trash icon on any task card to delete it.

### Theme & Language

- **Toggle Dark Mode**: Click the sun/moon icon in the header
- **Change Language**: Click the language button (EN/RU) in the header
- Your preferences are automatically saved!

## Project Structure

```
SmartTaskBoard/
├── app/
│   ├── Http/Controllers/
│   │   ├── BoardController.php    # Board management & CRUD
│   │   ├── ColumnController.php   # Column management & CRUD
│   │   ├── TaskController.php     # Task CRUD and drag-drop
│   │   └── TagController.php      # Tag management
│   └── Models/
│       ├── Board.php              # Board model with relationships
│       ├── Column.php             # Column model
│       ├── Task.php               # Task model with tags relationship
│       └── Tag.php                # Tag model
├── database/
│   └── migrations/                # Database schema
├── lang/
│   ├── en/
│   │   └── app.php               # English translations
│   └── ru/
│       └── app.php               # Russian translations
├── public/
│   └── js/
│       └── translations.js       # Frontend translations
├── resources/
│   ├── css/
│   │   └── app.css               # Tailwind styles
│   ├── js/
│   │   ├── app.js                # Alpine.js setup
│   │   └── bootstrap.js          # Laravel Echo config
│   └── views/
│       ├── layout.blade.php      # Main layout
│       └── board.blade.php       # Task board with dark mode & i18n
├── routes/
│   └── web.php                   # Application routes
└── tailwind.config.js            # Tailwind configuration
```

## Customization

### Adding New Languages

1. Create a new language file in `lang/{locale}/app.php`:

```php
<?php
return [
    'boards' => 'Your translation',
    'tasks' => 'Your translation',
    // ... other translations
];
```

2. Add translations to `public/js/translations.js`:

```javascript
const translations = {
    en: { /* English translations */ },
    ru: { /* Russian translations */ },
    your_locale: { /* Your translations */ }
};
```

3. Update the `toggleLanguage()` method in `board.blade.php` to include your language.

### Customizing Default Columns

Edit the `createDefaultBoard()` method in `BoardController.php`:

```php
$columns = [
    ['name' => 'To Do', 'color' => '#ef4444', 'position' => 0],
    ['name' => 'In Progress', 'color' => '#f59e0b', 'position' => 1],
    ['name' => 'Your New Column', 'color' => '#3b82f6', 'position' => 2],
];
```

### Changing Theme Colors

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

### Web Routes
- `GET /` - Display main board
- `GET /boards/{board}` - Display specific board

### Board Management
- `POST /boards` - Create new board
- `PUT /boards/{board}` - Update board
- `DELETE /boards/{board}` - Delete board
- `GET /api/boards` - Get all boards
- `GET /api/boards/{board}/data` - Get board data with all tasks

### Column Management
- `POST /columns` - Create new column
- `PUT /columns/{column}` - Update column
- `DELETE /columns/{column}` - Delete column

### Task Management
- `POST /tasks` - Create new task
- `PUT /tasks/{task}` - Update task
- `DELETE /tasks/{task}` - Delete task
- `POST /api/tasks/{task}/move` - Move task to different column/position
- `POST /api/tasks/{task}/reorder` - Reorder tasks within column

## Performance

- Optimized database queries with eager loading
- Smooth CSS transitions using Tailwind utilities
- Efficient drag-and-drop with SortableJS
- Minimal JavaScript footprint with Alpine.js
- LocalStorage for theme and language preferences (no server requests)
- Responsive design with mobile-first approach

## Features Implemented ✅

- ✅ **Multi-board management** - Create and switch between multiple boards
- ✅ **Dynamic column management** - Add, edit, and delete columns
- ✅ **Dark mode** - Full dark theme support with toggle
- ✅ **Multi-language** - English and Russian support
- ✅ **Drag & drop** - Move tasks between columns
- ✅ **Task priorities** - Low, Medium, High, Urgent
- ✅ **Custom colors** - Choose colors for columns
- ✅ **Persistent preferences** - Theme and language saved locally

## Future Enhancements

- [ ] Real-time collaboration with Pusher/Laravel Echo
- [ ] Task search and filtering
- [ ] User authentication
- [ ] Task assignments
- [ ] File attachments
- [ ] Activity timeline
- [ ] Export to PDF/CSV
- [ ] Task comments
- [ ] Keyboard shortcuts
- [ ] Task templates

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
