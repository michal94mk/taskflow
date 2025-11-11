# TaskFlow - Project Management Application

A modern, full-featured project management application built with Laravel 12 and Vue 3.

![TaskFlow Dashboard](https://via.placeholder.com/800x400?text=TaskFlow+Dashboard)

## ✨ Features

### Core Functionality
- 📊 **Dashboard** - KPI cards, charts (Chart.js), recent tasks, quick actions
- 📁 **Projects Management** - Full CRUD operations for projects
- ✅ **Tasks Management** - Complete task lifecycle management
- 🎯 **Kanban Board** - Drag & drop tasks between statuses
- 💬 **Comments System** - Rich text comments with Quill.js editor
- 🔍 **Global Search** - Fast search with Ctrl+K shortcut
- 🌓 **Dark Mode** - Toggle between light and dark themes
- 🔔 **Toast Notifications** - Real-time feedback for user actions

### Technical Features
- 🎨 Modern UI with Tailwind CSS and reka-ui components
- 🔐 Authentication with Laravel Fortify (2FA support)
- 📱 Fully responsive design
- ⚡ SPA experience with Inertia.js
- 🎭 TypeScript for type safety
- 🎨 ESLint for code quality

## 🛠️ Tech Stack

### Backend
- **Laravel 12** - PHP framework
- **SQLite** - Database (easily switchable to MySQL/PostgreSQL)
- **Laravel Fortify** - Authentication
- **Laravel Sanctum** - API authentication

### Frontend
- **Vue 3** - Progressive JavaScript framework
- **Inertia.js** - Modern monolith approach
- **TypeScript** - Type-safe JavaScript
- **Tailwind CSS** - Utility-first CSS framework
- **reka-ui** - Headless UI components
- **Chart.js** - Data visualization
- **Quill.js** - Rich text editor
- **vue-sonner** - Toast notifications
- **vuedraggable** - Drag & drop functionality

### Build Tools
- **Vite** - Fast build tool
- **ESLint** - Code linting
- **Prettier** - Code formatting

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite (or MySQL/PostgreSQL)

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/yourusername/taskflow.git
cd taskflow
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install JavaScript dependencies

```bash
npm install
```

### 4. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Database setup

```bash
# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed
```

### 6. Build assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start the development server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 📖 Usage

### Authentication

1. Register a new account at `/register`
2. Login at `/login`
3. Optional: Enable 2FA in settings

### Dashboard

The dashboard provides an overview of your tasks and projects:
- **KPI Cards** - Total tasks, completed, in progress, overdue
- **Charts** - Visual representation of tasks by status, priority, and timeline
- **Recent Tasks** - Quick access to your latest tasks
- **Quick Actions** - Fast navigation to common actions

### Projects

Create and manage projects:
- Navigate to **Projects** in the sidebar
- Click **New Project** to create
- Each project can have multiple tasks
- View project details and associated tasks

### Tasks

Manage your tasks:
- Navigate to **Tasks** in the sidebar
- Create tasks with title, description, priority, status, and due date
- Filter tasks by project, status, priority
- Search tasks by title or description
- Add comments with rich text formatting

### Kanban Board

Visual task management:
- Navigate to **Kanban Board** in the sidebar
- Drag and drop tasks between columns (statuses)
- Filter by project
- Tasks automatically update when moved

### Global Search

Quick navigation:
- Press **Ctrl+K** (or **⌘K** on Mac)
- Type to search tasks and projects
- Use arrow keys to navigate results
- Press Enter to open selected item

### Dark Mode

Toggle between light and dark themes:
- Click the sun/moon icon in the sidebar footer
- Preference is saved automatically

## 🗂️ Project Structure

```
taskflow/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # API and page controllers
│   │   └── Middleware/        # Custom middleware
│   ├── Models/                # Eloquent models
│   └── Policies/              # Authorization policies
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── js/
│   │   ├── components/        # Vue components
│   │   ├── composables/       # Vue composables
│   │   ├── layouts/           # Page layouts
│   │   ├── pages/             # Inertia pages
│   │   └── types/             # TypeScript types
│   └── css/
│       └── app.css            # Global styles
├── routes/
│   ├── web.php                # Web routes
│   └── api.php                # API routes
└── tests/                     # PHPUnit tests
```

## 🎨 Key Components

### Dashboard Components
- `KpiCard.vue` - Displays key performance indicators
- `ChartCard.vue` - Renders Chart.js visualizations
- `RecentTasks.vue` - Shows recent task list
- `QuickActions.vue` - Quick action buttons

### UI Components
- `GlobalSearch.vue` - Command palette search
- `ThemeToggle.vue` - Dark mode toggle
- `RichTextEditor.vue` - Quill.js wrapper

### Layout Components
- `AppLayout.vue` - Main application layout
- `AppSidebar.vue` - Navigation sidebar
- `AppSidebarHeader.vue` - Header with breadcrumbs and search

## 🔧 Configuration

### Database

Edit `.env` to change database configuration:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

For MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskflow
DB_USERNAME=root
DB_PASSWORD=
```

### Mail

Configure mail settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

## 🧪 Testing

Run PHPUnit tests:

```bash
php artisan test
```

Run specific test:

```bash
php artisan test --filter=DashboardTest
```

## 🚢 Deployment

### Build for production

```bash
npm run build
```

### Optimize Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Set permissions

```bash
chmod -R 775 storage bootstrap/cache
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 Git Workflow

This project uses Git Flow:
- `main` - Production-ready code
- `develop` - Development branch
- `feature/*` - Feature branches
- `hotfix/*` - Hotfix branches

## 🐛 Known Issues

- None at the moment

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👏 Acknowledgments

- [Laravel](https://laravel.com)
- [Vue.js](https://vuejs.org)
- [Inertia.js](https://inertiajs.com)
- [Tailwind CSS](https://tailwindcss.com)
- [reka-ui](https://reka-ui.com)
- [Chart.js](https://www.chartjs.org)
- [Quill](https://quilljs.com)

## 📧 Contact

Your Name - [@yourtwitter](https://twitter.com/yourtwitter)

Project Link: [https://github.com/yourusername/taskflow](https://github.com/yourusername/taskflow)

---

Made with ❤️ using Laravel and Vue

