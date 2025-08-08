# Sistem de Administratori - Task Management System

## Prezentare Generală

Sistemul de management al task-urilor implementează două tipuri de administratori cu permisiuni diferite:

### 1. Administratorul Aplicației (Application Admin)
- **Permisiuni complete** asupra întregului sistem
- Poate gestiona **toți utilizatorii** din sistem
- Poate gestiona **toate proiectele** din sistem
- Poate gestiona **toate task-urile** din sistem
- Poate modifica rolurile utilizatorilor (regular user, project admin, application admin)

### 2. Administratorul Proiectelor (Project Admin)
- **Permisiuni limitate** doar la proiectele pe care le-a creat
- Poate gestiona **doar proiectele proprii**
- Poate gestiona **doar task-urile din proiectele proprii**
- **Nu poate** gestiona utilizatori
- **Nu poate** accesa proiectele altor utilizatori

## Structura Bazei de Date

### Tabela `users`
- `admin_type` - enum: `'none'`, `'application'`, `'project'`
- `is_admin` - boolean (pentru compatibilitate cu sistemul vechi)

## Comenzi Artisan

### Listarea Administratorilor
```bash
php artisan user:list-admins
```

### Crearea unui Administrator de Aplicație
```bash
php artisan user:make-application-admin {email}
```

### Crearea unui Administrator de Proiecte
```bash
php artisan user:make-project-admin {email}
```

### Comandă Legacy (pentru compatibilitate)
```bash
php artisan user:make-admin {email}  # Creează un application admin
```

## Rute și Middleware

### Rute pentru Administratorul Aplicației
- Prefix: `/admin/application`
- Middleware: `application.admin`
- Rute disponibile:
  - `GET /admin/application` - Dashboard
  - `GET /admin/application/users` - Lista utilizatori
  - `GET /admin/application/users/{user}/edit` - Editare utilizator
  - `PUT /admin/application/users/{user}` - Actualizare utilizator
  - `DELETE /admin/application/users/{user}` - Ștergere utilizator
  - `GET /admin/application/projects` - Lista proiecte
  - `GET /admin/application/projects/{project}/edit` - Editare proiect
  - `PUT /admin/application/projects/{project}` - Actualizare proiect
  - `DELETE /admin/application/projects/{project}` - Ștergere proiect
  - `GET /admin/application/tasks` - Lista task-uri
  - `GET /admin/application/tasks/{task}/edit` - Editare task
  - `PUT /admin/application/tasks/{task}` - Actualizare task
  - `DELETE /admin/application/tasks/{task}` - Ștergere task

### Rute pentru Administratorul Proiectelor
- Prefix: `/admin/project`
- Middleware: `project.admin`
- Rute disponibile:
  - `GET /admin/project` - Dashboard
  - `GET /admin/project/projects` - Lista proiecte proprii
  - `GET /admin/project/projects/{project}/edit` - Editare proiect propriu
  - `PUT /admin/project/projects/{project}` - Actualizare proiect propriu
  - `DELETE /admin/project/projects/{project}` - Ștergere proiect propriu
  - `GET /admin/project/tasks` - Lista task-uri din proiectele proprii
  - `GET /admin/project/tasks/{task}/edit` - Editare task din proiect propriu
  - `PUT /admin/project/tasks/{task}` - Actualizare task din proiect propriu
  - `DELETE /admin/project/tasks/{task}` - Ștergere task din proiect propriu

## Metode în Modelul User

### Verificarea Rolurilor
```php
// Verifică dacă utilizatorul este administrator de aplicație
$user->isApplicationAdmin();

// Verifică dacă utilizatorul este administrator de proiecte
$user->isProjectAdmin();

// Verifică dacă utilizatorul este orice tip de administrator
$user->isAdmin();
```

### Verificarea Permisiunilor
```php
// Verifică dacă utilizatorul poate gestiona un proiect specific
$user->canManageProject($project);

// Verifică dacă utilizatorul poate gestiona un utilizator specific
$user->canManageUser($user);
```

## Interfața Utilizator

### Navigare în Layout Principal
- **Application Admin**: Link roșu "App Admin" în header
- **Project Admin**: Link albastru "Project Admin" în header
- **Regular User**: Fără link de admin

### Dashboard-uri Separate
- **Application Admin Dashboard**: `/admin/application`
  - Statistici complete despre sistem
  - Lista tuturor utilizatorilor și proiectelor
  - Acces la toate funcționalitățile de administrare

- **Project Admin Dashboard**: `/admin/project`
  - Statistici doar despre proiectele proprii
  - Lista proiectelor și task-urilor proprii
  - Acces limitat la funcționalitățile de administrare

## Securitate

### Middleware-uri
- `ApplicationAdminMiddleware`: Verifică dacă utilizatorul este application admin
- `ProjectAdminMiddleware`: Verifică dacă utilizatorul este project admin
- `AdminMiddleware`: Verifică dacă utilizatorul este orice tip de admin

### Verificări în Controller-e
- **ApplicationAdminController**: Nu are verificări suplimentare (permisiuni complete)
- **ProjectAdminController**: Verifică la fiecare operație dacă utilizatorul poate gestiona resursa

### Exemple de Verificări
```php
// În ProjectAdminController
if (!auth()->user()->canManageProject($project)) {
    abort(403, 'You can only edit your own projects.');
}

// În ApplicationAdminController
if ($user->id === auth()->id()) {
    return redirect()->route('application.admin.users')->with('error', 'You cannot delete your own account.');
}
```

## Migrarea de la Sistemul Vechi

Sistemul este compatibil cu sistemul vechi de administratori:
- Utilizatorii cu `is_admin = true` sunt considerați application admins
- Câmpul `is_admin` este păstrat pentru compatibilitate
- Comenzile vechi (`user:make-admin`) funcționează în continuare

## Utilizare

### 1. Crearea unui Application Admin
```bash
php artisan user:make-application-admin admin@example.com
```

### 2. Crearea unui Project Admin
```bash
php artisan user:make-project-admin project@example.com
```

### 3. Verificarea Administratorilor
```bash
php artisan user:list-admins
```

### 4. Accesarea Dashboard-urilor
- Application Admin: `/admin/application`
- Project Admin: `/admin/project`

## Note Importante

1. **Project Admin** nu poate gestiona utilizatori
2. **Project Admin** nu poate accesa proiectele altor utilizatori
3. **Application Admin** nu poate șterge propriul cont
4. Sistemul este compatibil cu sistemul vechi de administratori
5. Toate verificările de securitate sunt implementate la nivel de controller și middleware
