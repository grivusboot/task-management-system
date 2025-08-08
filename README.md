# Task Management System

A comprehensive Laravel-based task management system with a two-tier administrator system, featuring project management, task tracking, and user role management.

## 🚀 Features

### Core Functionality
- **Project Management**: Create, edit, and manage projects with status tracking
- **Task Management**: Full CRUD operations for tasks with priority levels and status tracking
- **User Management**: Complete user administration with role-based access control
- **Kanban Board**: Visual task management with drag-and-drop functionality
- **Real-time Updates**: Live updates for task status changes
- **Responsive Design**: Modern UI built with Tailwind CSS

### Two-Tier Administrator System
- **Application Administrator**: Full system access
  - Manage all users and projects
  - Create, edit, and delete any project
  - Assign tasks to any user
  - View system-wide statistics
- **Project Administrator**: Limited to own projects
  - Manage only projects they created
  - Create and edit tasks within their projects
  - View project-specific statistics

### User Roles & Permissions
- **Regular Users**: Can view assigned tasks and projects
- **Project Admins**: Can manage their own projects and tasks
- **Application Admins**: Can manage the entire system

## 🛠️ Technology Stack

- **Backend**: Laravel 12.x
- **Frontend**: Blade Templates, Tailwind CSS, Vue.js
- **Database**: MySQL/PostgreSQL
- **Build Tool**: Vite
- **PHP Version**: 8.2+

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Web server (Apache/Nginx)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/task-management-system.git
cd task-management-system
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install --legacy-peer-deps
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Build Assets
```bash
npm run build
```

### 7. Start Development Server
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## 👥 User Management

### Creating Administrators

Use the provided Artisan commands to manage user roles:

```bash
# Make a user an Application Administrator
php artisan user:make-application-admin user@example.com

# Make a user a Project Administrator
php artisan user:make-project-admin user@example.com

# List all administrators
php artisan user:list-admins

# Legacy: Make a user an admin (sets as Application Admin)
php artisan user:make-admin user@example.com
```

### User Registration
- Users can register through the `/register` route
- New users start with regular user permissions
- Administrators can promote users through the admin panel

## 🏗️ System Architecture

### Database Structure
- **Users**: User accounts with role management
- **Projects**: Project information and metadata
- **Tasks**: Task details with assignments and status
- **User Roles**: Project-specific user permissions
- **Subtasks**: Sub-task management within tasks

### Key Models
- `User`: User management with admin type system
- `Project`: Project management with creator tracking
- `Task`: Task management with assignment and status
- `UserRole`: Project-specific user permissions

### Middleware
- `ApplicationAdminMiddleware`: Restricts access to application admins
- `ProjectAdminMiddleware`: Restricts access to project admins
- `AdminMiddleware`: Legacy middleware for backward compatibility

## 🎨 UI Components

### Admin Dashboards
- **Application Admin Dashboard**: System-wide statistics and overview
- **Project Admin Dashboard**: Project-specific statistics and overview

### Navigation
- Responsive navigation with role-based menu items
- Breadcrumb navigation for better UX
- Mobile-friendly design

### Forms
- Validated forms with error handling
- Real-time validation feedback
- Consistent styling across all forms

## 🔧 Configuration

### Tailwind CSS
The project uses Tailwind CSS v3 for styling. Configuration files:
- `tailwind.config.js`: Tailwind configuration
- `postcss.config.js`: PostCSS configuration
- `vite.config.js`: Vite build configuration

### Environment Variables
Key environment variables to configure:
```env
APP_NAME="Task Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 🚀 Deployment

### Production Setup
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Configure your web server (Apache/Nginx)
4. Set up SSL certificate
5. Configure database for production
6. Run `npm run build` for production assets

### Web Server Configuration
Example Nginx configuration:
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/task-management-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 🔒 Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **Role-Based Access Control**: Granular permissions system
- **Input Validation**: Comprehensive validation on all inputs
- **SQL Injection Protection**: Eloquent ORM with parameterized queries
- **XSS Protection**: Output escaping in Blade templates

## 📊 API Endpoints

The system provides RESTful API endpoints for:
- User management
- Project management
- Task management
- Status updates

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue on GitHub
- Check the documentation
- Review the Laravel documentation

## 🔄 Changelog

### Version 1.0.0
- Initial release
- Two-tier administrator system
- Complete project and task management
- User role management
- Responsive design with Tailwind CSS
- Kanban board functionality
- Real-time updates

## 🙏 Acknowledgments

- Laravel team for the amazing framework
- Tailwind CSS for the utility-first CSS framework
- Vue.js for the reactive frontend components
- All contributors and users of this project

---

**Made with ❤️ using Laravel and Tailwind CSS**
