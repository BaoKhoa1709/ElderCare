# 🏥 ElderSync (Legacy Collaboration Work)

**ElderSync** is a **care management platform** that connects **Care Seekers** (those in need of care) with **Care Givers** (those who provide care).  
It is built as a **RESTful Spring Boot backend** with a **React frontend**, featuring AI-powered matching, booking lifecycle management, secure authentication, and payment integration.

---

## 🚀 System Architecture

ElderSync follows a **client–server architecture**:

- **Frontend** → React.js (UI, user interaction, API consumption)  
- **Backend** → Spring Boot (RESTful API services)  
- **Database** → MySQL with JPA/Hibernate (data persistence)  
- **Authentication** → Stateless JWT tokens  

---

## 🛠️ Core Technologies

| Component       | Technology                  | Purpose |
|-----------------|-----------------------------|---------|
| Frontend        | React.js + Html, CSS        | Client-side app, API requests |
| Backend         | Spring Boot (RESTful APIs)  | Business logic & endpoints |
| Database        | MySQL + Hibernate/JPA       | Data storage |
| Security        | Spring Security + JWT       | Authentication & RBAC |
| API Docs        | Swagger / OpenAPI           | REST API documentation |
| Cloud Storage   | Cloudinary                  | Profile image hosting |
| Mail Service    | Gmail SMTP                  | Email notifications |
| AI Matching     | Groq AI (llama3-8b-8192)    | Intelligent caregiver matching |
| Payments        | MoMo Gateway                | Online transactions |

---

## 🧩 Key Features

### 🤖 AI-Powered Matching
- Matches Care Seekers and Care Givers based on:
  - Skills  
  - Health conditions  
  - Gender preferences  
  - Location  
- Uses Groq’s **llama3-8b-8192** model.  
- Matching threshold: **≥ 40 points**.

### 📅 Booking Lifecycle
- Create and manage bookings.  
- Payment processing via **MoMo**.  
- Integrated **task management** within bookings.  
- Automated **email notifications**.

### 👥 Multi-Role User System
- **USER** → Basic account role: register, login, manage personal profile.  
- **SEEKER** → Create care requests, get AI-powered caregiver recommendations, manage bookings & payments, receive notifications.  
- **GIVER** → Build caregiver profile, accept/manage bookings, handle assigned tasks, receive alerts.  
- **ADMIN** → Manage users & caregivers, oversee bookings/tasks, and maintain system control.  


### 🔒 Security
- Stateless **JWT authentication**  
- Role-based access control (RBAC)  
- Method-level security with Spring annotations  
- Configurable CORS support  

---

## 📄 License
This project is licensed under the MIT License.
See the LICENSE file for details.
