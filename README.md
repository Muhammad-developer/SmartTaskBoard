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
- **✏️ Task Editing**: Click on any task to edit it inline
- **🔍 Search & Filter**: Real-time search and filter by priority
- **🏷️ Tag Management**: Create, manage, and organize tasks with custom tags
- **📢 Toast Notifications**: User-friendly notifications for all actions
- **⌨️ Keyboard Shortcuts**: Fast navigation with keyboard shortcuts (N, B, T, D, L, /, ESC)
- **📱 Mobile Optimized**: Touch-friendly drag & drop for mobile devices
- **⏰ Overdue Indicators**: Visual indicators for tasks past their due date
- **🎯 Empty States**: Helpful placeholders when columns or boards are empty
- **🌐 Locale-aware Dates**: Dates formatted according to selected language

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

1. Click the "New Task" button in the header (or press **N**)
2. Fill in the task details:
   - Select a column
   - Enter task title (required)
   - Add description (optional)
   - Set priority level (Low, Medium, High, Urgent)
   - Set due date (optional)
3. Click "Create Task"

### Editing Tasks

1. Click on any task title or the edit icon
2. Modify any task details
3. Click "Save" to update

### Managing Tags

1. Click the "Tags" button in the header (or press **T**)
2. View all existing tags
3. Create new tags with custom names and colors
4. Delete unused tags

### Moving Tasks

Simply drag and drop tasks between columns! The position will be saved automatically.

### Searching and Filtering

1. Use the search bar to find tasks by title or description
2. Filter tasks by priority using the dropdown
3. Click "Clear Filters" to reset

### Deleting Tasks

Click the trash icon on any task card to delete it (confirmation required).

### Theme & Language

- **Toggle Dark Mode**: Click the sun/moon icon in the header (or press **D**)
- **Change Language**: Click the language button (EN/RU) in the header (or press **L**)
- Your preferences are automatically saved!

### Keyboard Shortcuts

Access common actions quickly with keyboard shortcuts:

| Shortcut | Action |
|----------|--------|
| **N** | Create new task |
| **B** | Open boards menu |
| **T** | Manage tags |
| **D** | Toggle dark mode |
| **L** | Switch language |
| **/** | Focus search bar |
| **ESC** | Close any modal |

*Click the help button (bottom-right corner) to view shortcuts anytime*

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
- ✅ **Task CRUD operations** - Create, read, update, and delete tasks
- ✅ **Task editing** - Click to edit tasks inline
- ✅ **Tag system** - Create and manage custom tags with colors
- ✅ **Search functionality** - Real-time search across task titles and descriptions
- ✅ **Priority filtering** - Filter tasks by priority level
- ✅ **Dark mode** - Full dark theme support with smooth transitions
- ✅ **Multi-language** - English and Russian support with locale-aware dates
- ✅ **Drag & drop** - Smooth task movement between columns
- ✅ **Task priorities** - Low, Medium, High, Urgent levels
- ✅ **Custom colors** - Choose colors for columns and tags
- ✅ **Persistent preferences** - Theme and language saved in localStorage
- ✅ **Toast notifications** - Real-time user feedback for all actions
- ✅ **Keyboard shortcuts** - Fast navigation with hotkeys
- ✅ **Empty states** - Helpful placeholders for empty columns and boards
- ✅ **Overdue indicators** - Visual markers for tasks past due date
- ✅ **Loading states** - Spinner overlay during async operations
- ✅ **Mobile responsive** - Touch-friendly drag & drop for mobile devices
- ✅ **Smooth animations** - Fade-in and slide-up effects
- ✅ **Custom scrollbars** - Themed scrollbars for better aesthetics

## Future Enhancements

- [ ] Real-time collaboration with Pusher/Laravel Echo
- [ ] User authentication and permissions
- [ ] Task assignments to team members
- [ ] File attachments for tasks
- [ ] Activity timeline and history
- [ ] Export board to PDF/CSV/JSON
- [ ] Task comments and discussions
- [ ] Task templates for quick creation
- [ ] Subtasks/checklists within tasks
- [ ] Task statistics and analytics dashboard
- [ ] Due date reminders and notifications
- [ ] Calendar view
- [ ] Time tracking
- [ ] Task dependencies
- [ ] Recurring tasks

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
