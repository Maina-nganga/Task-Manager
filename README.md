Task Management API

A RESTful API built with Laravel for managing tasks through their full lifecycle — from creation to completion. The API enforces strict business rules around task status progression, deletion, and duplicate prevention.
Live URL: https://task-manager-api-wpuy.onrender.com

Note: The app is hosted on Render's free tier and may take up to 50 seconds to respond after a period of inactivity.


Table of Contents

Features
Tech Stack
Running Locally
Deploying to Render
API Endpoints
Example API Requests
Database Schema


Features

Create tasks with title, due date, and priority
Prevent duplicate tasks with the same title and due date
List and filter tasks by status, sorted by priority and due date
Restrict deletion to completed tasks only
Generate a daily summary report grouped by priority and status


Tech Stack

Framework: Laravel 10
Language: PHP 8.1+
Local Database: MySQL
Production Database: PostgreSQL (via Render)
Hosting: Render (Docker)
Version Control: GitHub


Running Locally
Prerequisites

PHP >= 8.1
Composer
MySQL
Git

Steps
1. Clone the repository
bashgit clone https://github.com/Maina-nganga/Task-Manager.git
cd Task-Manager

2. Install dependencies
bashcomposer install

3. Set up the environment file
bashcp .env.example .env
Open .env and update the following with your local MySQL credentials:
envAPP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:5OoOnQeMVIK7OECF71m607FJmEm1astxv1NWJnDNP30=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

4. Generate the application key
bashphp artisan key:generate

5. Create the database
sqlCREATE DATABASE task_manager;

6. Run migrations
bashphp artisan migrate

7.Seed the database
bashphp artisan db:seed

8. Start the development server
bashphp artisan serve
The application will be available at http://localhost:8000.

Deploying to Render

Prerequisites:
A GitHub account with the project repository pushed
A Render account at render.com

Steps
1. Create a PostgreSQL database on Render
Go to your Render dashboard and click New +, then select PostgreSQL. Set the name to task-manager-db, the region to Oregon, and the instance type to Free. Click Create Database and copy the credentials from the Connections section once it is ready.

2. Create a Web Service on Render
Click New + and select Web Service. Connect your GitHub repository Maina-nganga/Task-Manager. Set the language to Docker and the region to Oregon.

3. Add Environment Variables
In your web service, go to the Environment tab and add the following variables one by one:
APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:5OoOnQeMVIK7OECF71m607FJmEm1astxv1NWJnDNP30=
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com
DB_CONNECTION=pgsql
DB_HOST=<Hostname from Render DB Connections>
DB_PORT=5432
DB_DATABASE=<Database from Render DB Connections>
DB_USERNAME=<Username from Render DB Connections>
DB_PASSWORD=<Password from Render DB Connections>
LOG_CHANNEL=stack
LOG_LEVEL=error
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

4. Deploy
Click Manual Deploy and select Deploy latest commit. Database migrations will run automatically on startup via start.sh.

API Endpoints
POST /api/tasks
Creates a new task. The title must be unique for the given due date. The due date must be today or a future date. Returns the created task on success.

GET /api/tasks
Returns all tasks sorted by priority (high to low) and then by due date in ascending order. Accepts an optional status query parameter to filter results (e.g. ?status=pending). Returns a meaningful message if no tasks exist.

PATCH /api/tasks/{id}/status
Updates the status of a task. Status must progress in order — pending to in_progress, then in_progress to done. Skipping or reverting status is not permitted.

DELETE /api/tasks/{id}
Deletes a task. Only tasks with a status of done can be deleted. Attempting to delete a task that is not done will return a 403 Forbidden response.

GET /api/tasks/report?date=YYYY-MM-DD
Returns a daily summary of tasks for the given date, grouped by priority and status. (Bonus endpoint)

Example API Requests
Create a Task
bashcurl -X POST https://task-manager-api-wpuy.onrender.com/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Fix login bug",
    "due_date": "2026-04-05",
    "priority": "high"
  }'
Response:
json{
  "id": 1,
  "title": "Fix login bug",
  "due_date": "2026-04-05",
  "priority": "high",
  "status": "pending",
  "created_at": "2026-04-01T10:00:00Z",
  "updated_at": "2026-04-01T10:00:00Z"
}

List All Tasks
bashcurl https://task-manager-api-wpuy.onrender.com/api/tasks
Filter Tasks by Status
bashcurl https://task-manager-api-wpuy.onrender.com/api/tasks?status=pending
Update Task Status
bashcurl -X PATCH https://task-manager-api-wpuy.onrender.com/api/tasks/1/status \
  -H "Content-Type: application/json" \
  -d '{"status": "in_progress"}'

Delete a Task
bashcurl -X DELETE https://task-manager-api-wpuy.onrender.com/api/tasks/1
Daily Report
bashcurl https://task-manager-api-wpuy.onrender.com/api/tasks/report?date=2026-04-01
Response:
json{
  "date": "2026-04-01",
  "summary": {
    "high":   { "pending": 2, "in_progress": 1, "done": 0 },
    "medium": { "pending": 1, "in_progress": 0, "done": 3 },
    "low":    { "pending": 0, "in_progress": 0, "done": 1 }
  }
}


Database Schema
The API uses a single tasks table with the following columns:

id — integer, primary key, auto-incremented
title — string, the task title
due_date — date, the task deadline
priority — enum, one of low, medium, or high
status — enum, one of pending, in_progress, or done
created_at — timestamp, automatically managed by Laravel
updated_at — timestamp, automatically managed by Laravel

