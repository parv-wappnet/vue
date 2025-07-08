```markdown
# Vue 3 Chat App Frontend

This is the frontend for a real-time chat application built with Vue 3. It provides a user-friendly interface for login, registration, private and group chats, and more, with real-time updates powered by Pusher and Laravel Echo.

## 📌 Features
- **Login / Register with token-based auth**: Secure authentication using JWT or Sanctum tokens.
- **Real-time chat (private & group)**: Instant messaging for one-to-one and multi-user conversations.
- **Lazy loading messages on scroll**: Load older messages as users scroll up.
- **Online user indicator**: Show the online status of users.
- **Fully responsive UI (mobile-friendly)**: Optimized design for all screen sizes.
- **Message date dividers**: Display dates between message groups for better context.
- **Group & profile tabs with router**: Navigate between group chats and user profiles.
- **Auto scroll to bottom on new messages**: Automatically scroll to the latest message.

## ⚙️ Tech Stack
- **Framework**: Vue 3 + Composition API
- **Routing**: Vue Router
- **State Management**: Pinia (store)
- **HTTP Requests**: Axios
- **Real-time**: Pusher + Laravel Echo
- **Styling**: Tailwind CSS (or custom CSS)

## 🚀 Setup Instructions

### Prerequisites
- Node.js >= 18.x
- npm
- Access to the Laravel Chat API backend (e.g., `http://localhost:8000/api`)

### Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-username/chatapp-frontend.git
   cd chatapp-frontend
   ```

2. **Install Dependencies**
   ```bash
   npm install
   ```

3. **Create .env File**
   ```bash
   cp .env.example .env
   ```

4. **Run the Development Server**
   ```bash
   npm run dev
   ```