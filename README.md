Task Management API:

A robust RESTful API built with Laravel for managing tasks through their full lifecycle — from creation to completion. The API enforces strict business rules around task status progression, deletion, and duplicate prevention.

Live URL: https://task-manager-api-wpuy.onrender.com  

 Note:The app is hosted on Render’s free tier and may take up to 50 seconds to respond after inactivity.



 Table of Contents:

- [Features](-features)
- [Tech Stack](-tech-stack)
- [Running Locally](-running-locally)
- [Deploying to Render](-deploying-to-render)
- [API Endpoints](-api-endpoints)
- [Example API Requests](-example-api-requests)
- [Database Schema](-database-schema)



 Features:

- Create tasks with title, due date, and priority  
- Prevent duplicate tasks with the same title and due date  
- List and filter tasks by status  
- Sort tasks by priority and due date  
- Restrict deletion to completed tasks only  
- Generate a daily summary report grouped by priority and status  


 Tech Stack:

- Framework:Laravel 10  
- Language: PHP 8.1+  
- Local Database: MySQL  
- Production Database: PostgreSQL (Render)  
- Hosting: Render (Docker)  
- Version Control: GitHub  



 Running Locally

 Prerequisites

- PHP >= 8.1  
- Composer  
- MySQL  
- Git  

 Installation Steps:

 1. Clone the repository
` bash
git clone https://github.com/Maina-nganga/Task-Manager.git
cd Task-Manager


2.Install dependencies:
  composer install

3.Set up environment variables:
  cp .env.example .env

Update .env with your MySQL credentials:

APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=your_mysql_password


4.Generate application key:
  php artisan key:generate

5 Create database:
  CREATE DATABASE task_manager;

6.Run migrations:
  php artisan migrate

7 Seed database
  php artisan db:seed  

8.Start development server
  php artisan serve

  App will be available at:
  http://localhost:8000


 Deploying to Render:

Prerequisites
GitHub repository
Render account

Deployment Steps:
1. Create PostgreSQL Database:
Go to Render Dashboard  New +  PostgreSQL
Name: task-manager-db
Region: Oregon
Plan: Free
Copy credentials from Connections

2. Create Web Service:
New + → Web Service
Connect repo: Maina-nganga/Task-Manager
Environment: Docker

3. Add Environment Variables:
APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:your_generated_key
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com

DB_CONNECTION=pgsql
DB_HOST=<Render DB Host>
DB_PORT=5432
DB_DATABASE=<Render DB Name>
DB_USERNAME=<Render DB User>
DB_PASSWORD=<Render DB Password>

LOG_CHANNEL=stack
LOG_LEVEL=error
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

4. Deploy:
Click Manual Deploy → Deploy latest commit
Migrations run automatically via start.sh 

API Endpoints:

Create Task:
POST /api/tasks

Title must be unique per due date
Due date must be today or future

Get All Tasks:
GET /api/tasks
Sorted by priority (high to low) and due date
Optional filter:
/api/tasks?status=pending

Update Task Status:
PATCH /api/tasks/{id}/status

Allowed flow:
pending → in_progress → done
No skipping or reverting

Delete Task:
DELETE /api/tasks/{id}
Only allowed if status = done
Otherwise returns 403 Forbidden

Daily Report:
GET /api/tasks/report?date=YYYY-MM-DD
Returns grouped summary by priority and status


example API Requests:

Create Task:
curl -X POST https://task-manager-api-wpuy.onrender.com/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Fix login bug",
    "due_date": "2026-04-05",
    "priority": "high"
  }'


Get Tasks:
curl https://task-manager-api-wpuy.onrender.com/api/tasks


Filter Tasks:
curl https://task-manager-api-wpuy.onrender.com/api/tasks?status=pending


Update Status:
curl -X PATCH https://task-manager-api-wpuy.onrender.com/api/tasks/1/status \
  -H "Content-Type: application/json" \
  -d '{"status": "in_progress"}
  
  '
Delete Task:
curl -X DELETE https://task-manager-api-wpuy.onrender.com/api/tasks/1


Daily Report:
curl https://task-manager-api-wpuy.onrender.com/api/tasks/report?date=2026-04-01




Database Schema:

tasks table

| Column      | Type      | Description |
|------------|----------|-------------|
| id         | Integer  | Primary key (auto-increment) |
| title      | String   | Task title |
| due_date   | Date     | Task deadline |
| priority   | Enum     | low, medium, high |
| status     | Enum     | pending, in_progress, done |
| created_at | Timestamp | Managed by Laravel |
| updated_at | Timestamp | Managed by Laravel |