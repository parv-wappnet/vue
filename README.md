```markdown
# Laravel Chat API Backend

This is the backend API for a real-time chat application built with Laravel. It provides a robust foundation for private and group chats, user authentication, and real-time messaging using Laravel Broadcasting and Pusher.


## 📌 Features
- **JWT-authenticated user registration & login**: Secure user authentication using JWT or Laravel Sanctum.
- **Private & group chat support**: Enable one-to-one and multi-user conversations.
- **Real-time messaging**: Powered by Laravel Broadcasting and Pusher for instant message delivery.
- **Message pagination with lazy loading**: Load older messages as users scroll up.
- **Group creation and member management**: Create groups and manage participants.
- **Follows and connection requests**: Allow users to connect before private chats.
- **Secure routes**: Protected with Laravel Sanctum or JWT authentication.
- **Broadcasts real-time events**: Includes events like `MessageSent`, `GroupCreated`, etc.

## ⚙️ Tech Stack
- **Framework**: Laravel 10
- **Database**: MySQL
- **Real-time**: Laravel Broadcasting + Pusher
- **Authentication**: Laravel Sanctum / JWTAuth (configurable)
- **API**: RESTful API

## 🚀 Setup Instructions

### Prerequisites
- PHP >= 8.1
- Composer
- Node.js and npm (for frontend if integrated)
- MySQL or PostgreSQL
- Pusher account (for real-time broadcasting)

### Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-username/chatapp-backend.git
   cd chatapp-backend
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```
 ```bash
   cp .env.example .env
   ```
 ```bash
   php artisan key:generate
   ```
 ```bash
   php artisan serve
   ```
 ```bash
   php artisan queue:work
   ```
   Run this in a separate terminal to process broadcast events.
   For production, consider using a process manager like Supervisor.
