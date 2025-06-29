# Laravel 10 Chat App API Documentation

## Overview

This is a comprehensive chat application API built with Laravel 10, featuring real-time messaging, user authentication, and WebSocket support.

## Features

- **User Authentication**: Registration, login, and Google OAuth integration
- **Real-time Chat**: WebSocket-based messaging with Laravel WebSockets
- **Conversation Management**: Private and group conversations
- **File Sharing**: Support for images, documents, audio, and video files
- **User Status**: Online/offline status tracking
- **Message Management**: Send, edit, and delete messages
- **API Resources**: Consistent JSON responses with proper formatting

## API Endpoints

### Authentication

#### Register User
```
POST /api/v1/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login User
```
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

#### Google OAuth
```
GET /api/v1/auth/google/redirect
GET /api/v1/auth/google/callback
```

### User Management

#### Get Current User
```
GET /api/v1/user/me
Authorization: Bearer {token}
```

#### Set Password (for OAuth users)
```
POST /api/v1/user/set-password
Authorization: Bearer {token}
Content-Type: application/json

{
    "password": "newpassword123"
}
```

#### Logout
```
POST /api/v1/user/logout
Authorization: Bearer {token}
```

### Conversations

#### Get User Conversations
```
GET /api/v1/conversations
Authorization: Bearer {token}
```

#### Create Conversation
```
POST /api/v1/conversations
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Group Chat", // Optional for group chats
    "type": "group", // "private" or "group"
    "description": "Optional description", // Optional
    "user_ids": [2, 3, 4] // Array of user IDs to add
}
```

#### Get Available Users
```
GET /api/v1/conversations/available-users
Authorization: Bearer {token}
```

#### Get Conversation Details
```
GET /api/v1/conversations/{conversation_id}
Authorization: Bearer {token}
```

#### Update Conversation
```
PUT /api/v1/conversations/{conversation_id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Updated Group Name",
    "description": "Updated description"
}
```

#### Delete Conversation
```
DELETE /api/v1/conversations/{conversation_id}
Authorization: Bearer {token}
```

### Messages

#### Get Conversation Messages
```
GET /api/v1/conversations/{conversation_id}/messages
Authorization: Bearer {token}
```

#### Send Message
```
POST /api/v1/conversations/{conversation_id}/messages
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "content": "Hello, world!",
    "type": "text" // "text", "image", "file", "audio", "video"
    // OR for file uploads:
    "file": [file],
    "type": "image"
}
```

#### Get Message
```
GET /api/v1/conversations/{conversation_id}/messages/{message_id}
Authorization: Bearer {token}
```

#### Update Message
```
PUT /api/v1/conversations/{conversation_id}/messages/{message_id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "content": "Updated message content"
}
```

#### Delete Message
```
DELETE /api/v1/conversations/{conversation_id}/messages/{message_id}
Authorization: Bearer {token}
```

## WebSocket Events

### Message Events
- **Channel**: `private-conversation.{conversation_id}`
- **Event**: `message.sent`
- **Data**: Message object with user information

### User Status Events
- **Channel**: `user-status`
- **Event**: `user.status.changed`
- **Data**: User object with new status

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User's display name
- `email` - Unique email address
- `password` - Hashed password (nullable for OAuth users)
- `google_id` - Google OAuth ID (nullable)
- `avatar` - Profile picture URL (nullable)
- `status` - Online status (online/offline/away)
- `last_seen_at` - Last activity timestamp
- `email_verified_at` - Email verification timestamp
- `remember_token` - Remember me token
- `created_at`, `updated_at` - Timestamps

### Conversations Table
- `id` - Primary key
- `name` - Conversation name (nullable for private chats)
- `type` - Conversation type (private/group)
- `description` - Conversation description (nullable)
- `created_by` - User ID who created the conversation
- `is_active` - Whether conversation is active
- `created_at`, `updated_at` - Timestamps

### Conversation User Pivot Table
- `conversation_id` - Foreign key to conversations
- `user_id` - Foreign key to users
- `role` - User role in conversation (admin/member)
- `joined_at` - When user joined the conversation
- `created_at`, `updated_at` - Timestamps

### Messages Table
- `id` - Primary key
- `conversation_id` - Foreign key to conversations
- `user_id` - Foreign key to users
- `content` - Message content
- `type` - Message type (text/image/file/audio/video)
- `file_url` - File URL (nullable)
- `file_name` - Original file name (nullable)
- `file_size` - File size in bytes (nullable)
- `is_edited` - Whether message was edited
- `edited_at` - Edit timestamp (nullable)
- `is_deleted` - Whether message is soft deleted
- `deleted_at` - Deletion timestamp (nullable)
- `created_at`, `updated_at` - Timestamps

## Setup Instructions

1. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

4. **File Storage**
   ```bash
   php artisan storage:link
   ```

5. **WebSocket Configuration**
   - Configure Laravel WebSockets in `config/websockets.php`
   - Set up broadcasting in `config/broadcasting.php`

6. **Queue Setup**
   ```bash
   php artisan queue:work
   ```

7. **Schedule Setup**
   ```bash
   # Add to crontab for user status updates
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

## Response Format

All API responses follow this consistent format:

```json
{
    "data": {
        // Response data
    },
    "message": "Success message",
    "meta": {
        // Pagination metadata (when applicable)
        "current_page": 1,
        "last_page": 5,
        "per_page": 20,
        "total": 100
    }
}
```

## Error Handling

Errors are returned with appropriate HTTP status codes:

- `400` - Bad Request (validation errors)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found (resource doesn't exist)
- `422` - Unprocessable Entity (validation failed)
- `500` - Internal Server Error

Error response format:
```json
{
    "message": "Error description",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

## Security Features

- **Token-based Authentication** using Laravel Sanctum
- **CSRF Protection** for web routes
- **Input Validation** with Form Requests
- **Authorization** checks for all protected resources
- **File Upload Security** with size and type restrictions
- **Soft Deletes** for messages to prevent data loss

## Performance Optimizations

- **Eager Loading** of relationships to prevent N+1 queries
- **Database Indexing** on frequently queried columns
- **Pagination** for large datasets
- **Caching** support for user status and conversations
- **Queue Jobs** for heavy operations

## Testing

Run the test suite:
```bash
php artisan test
```

## Deployment

1. Set production environment variables
2. Run database migrations
3. Configure WebSocket server
4. Set up queue workers
5. Configure cron jobs for scheduled tasks
6. Set up file storage (S3 recommended for production)

## Support

For issues and questions, please refer to the Laravel documentation or create an issue in the project repository. 