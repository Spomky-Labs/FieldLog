# FieldLog

**FieldLog** is a lightweight web application for taking field notes and attaching media.  
It is built with [Symfony](https://symfony.com/) and provides a clean foundation for progressively adding modern web features.

---

## 🚀 What is FieldLog?

FieldLog is a minimalist app for creating "entries" consisting of:

- A **title**
- A **text note**
- One or more **media files** (images, screenshots, etc.)
- An optional **geolocation**

Entries can be created, listed, and viewed in detail.  
The application is intentionally kept simple to serve as a learning or prototyping base.

---

## 🧩 Features

- Create a new note with a title, content, and optional media
- View the list of notes (newest first)
- See individual note details, including attachments
- Optional GPS coordinates support (when available)
- Responsive UI with Tailwind CSS
- Stimulus controllers for progressive enhancements

---

## 📦 Tech stack

- Symfony 7.3 (PHP 8.4+)
- Symfony UX
- Doctrine ORM + SQLite (default)
- DaisyUi

---

## 🛠️ Getting started

Please install Docker and Castor.

```bash
git clone git@github.com:Spomky-Labs/FieldLog.git
cd FieldLog
castor install
castor build
castor start
```

You can now access the app at:
👉 https://localhost

📝 License

This project is open-source and licensed under the MIT License.
