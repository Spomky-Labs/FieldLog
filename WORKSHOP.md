# PWA Workshop: Building Progressive Web Apps with Symfony

## Workshop Overview

This full-day workshop focuses on transforming the FieldLog application into a fully-featured Progressive Web App (PWA) using the Spomky-Labs/PWA-Bundle for Symfony.

**Duration**: 6-8 hours (1 full day)
**Level**: Intermediate
**Prerequisites**: Basic knowledge of Symfony, JavaScript, and web development

## Learning Objectives

By the end of this workshop, participants will:
- Understand PWA core concepts and architecture
- Install and configure the Spomky-Labs/PWA-Bundle
- Create a complete Web App Manifest
- Implement Service Workers with multiple caching strategies
- Enable offline functionality
- Implement Background Sync for offline content creation
- Configure Push Notifications
- Integrate system-level features (Share Target, Web Share, Badging)
- Optimize PWA performance and achieve high Lighthouse scores

---

## Morning Session: PWA Foundations (3-4 hours)

### 1. Introduction & Setup (30 minutes)

**Topics Covered:**
- What is a Progressive Web App?
- Benefits of PWAs vs Native Apps
- PWA Requirements (HTTPS, Service Worker, Manifest)
- Introduction to Spomky-Labs/PWA-Bundle

**Hands-on:**
- Install the bundle: `composer require spomky-labs/pwa-bundle`
- Initial configuration in `config/packages/pwa.yaml`
- Explore bundle structure and documentation

### 2. Web App Manifest (1 hour)

**Topics Covered:**
- Manifest.json structure and purpose
- App metadata configuration
- Display modes (standalone, fullscreen, minimal-ui, browser)
- Color theming (theme_color, background_color)
- Icons and maskable icons
- Installability criteria

**Hands-on:**
- Configure app metadata (name, short_name, description)
- Create app icons in multiple sizes (192x192, 512x512)
- Generate maskable icons for Android
- Set theme and background colors
- Configure start_url and scope
- Test installability on mobile and desktop devices

**Deliverables:**
- Complete manifest.json file
- Icon set in appropriate sizes
- Installable app on at least one device

### 3. Service Worker Basics (1.5 hours)

**Topics Covered:**
- Service Worker lifecycle (install, activate, fetch)
- Service Worker scope and registration
- Cache API fundamentals
- Caching strategies:
  - Cache-First (for static assets)
  - Network-First (for dynamic content)
  - Stale-While-Revalidate (for frequently updated content)
  - Network-Only, Cache-Only
- Precaching critical resources

**Hands-on:**
- Configure Service Worker via the bundle
- Implement Cache-First strategy for static assets
- Implement Network-First strategy for API calls
- Precache essential assets (CSS, JS, fonts)
- Debug Service Worker using DevTools
- Test cache behavior and updates

**Deliverables:**
- Working Service Worker with multiple strategies
- Precached essential assets
- Understanding of SW lifecycle

### 4. Basic Offline Mode (1 hour)

**Topics Covered:**
- Offline detection strategies
- Fallback pages and resources
- Offline UX best practices
- Informing users about connectivity status

**Hands-on:**
- Create an offline fallback page
- Implement network status detection
- Cache previously viewed entries for offline access
- Display offline indicator in the UI
- Test complete offline experience

**Deliverables:**
- Offline fallback page
- Network status indicator
- Cached content accessible offline

---

## Afternoon Session: Advanced PWA Features (3-4 hours)

### 5. Background Synchronization (1.5 hours)

**Topics Covered:**
- Background Sync API overview
- Use cases for Background Sync
- Implementing sync queues
- Handling sync failures
- User feedback during sync

**Hands-on (Perfect for FieldLog):**
- Implement offline entry creation
- Queue entries when offline
- Sync entries automatically when online
- Display sync status to users
- Handle sync errors gracefully
- Notify users after successful sync

**Deliverables:**
- Working Background Sync for entry creation
- Sync queue management
- User notifications after sync

### 6. Push Notifications (1 hour)

**Topics Covered:**
- Push Notifications architecture
- VAPID keys and subscription
- Permission management
- Notification best practices
- Integration with Symfony Notifier

**Hands-on:**
- Generate VAPID keys
- Request notification permissions
- Subscribe users to push notifications
- Send notifications from server (Symfony Notifier)
- Handle notification clicks and actions
- Implement daily reminder notification

**Deliverables:**
- Working push notification system
- User subscription management
- Server-side notification sending

### 7. System Integration Features (1 hour)

**Topics Covered:**
- Share Target API (receive shared content)
- Web Share API (share from your app)
- File Handling API
- Badging API
- App shortcuts

**Hands-on (FieldLog-specific):**
- Configure Share Target to receive text and files
- Create controller to handle shared content
- Implement Web Share to share entries
- Add app shortcuts for different entry types
- Implement badge counter for new items

**Configuration Examples:**
```json
"share_target": {
  "action": "/entry/new",
  "method": "POST",
  "enctype": "multipart/form-data",
  "params": {
    "title": "title",
    "text": "content",
    "files": [{
      "name": "file",
      "accept": ["image/*", "application/pdf"]
    }]
  }
}

"shortcuts": [
  {
    "name": "New Text Entry",
    "url": "/entry/new?type=text",
    "icons": [{"src": "/icons/text.png", "sizes": "192x192"}]
  }
]
```

**Deliverables:**
- Share Target receiving content from other apps
- Web Share functionality
- App shortcuts configured
- Badge API integration

### 8. Optimization & Best Practices (30 minutes)

**Topics Covered:**
- App Shell pattern
- Lazy loading strategies
- Resource preloading
- Lighthouse PWA audit
- Performance optimization
- Accessibility considerations
- Progressive enhancement approach

**Hands-on:**
- Run Lighthouse audit
- Optimize asset loading
- Implement lazy loading for images
- Preload critical resources
- Fix Lighthouse warnings
- Aim for 90+ PWA score

**Deliverables:**
- Lighthouse PWA score > 90
- Optimized loading performance
- Accessible PWA

---

## Practical Challenges

Throughout the workshop, participants will complete these challenges:

1. **Challenge 1**: Make the app fully functional offline (reading and creating entries)
2. **Challenge 2**: Implement Share Target to receive shared content
3. **Challenge 3**: Create a daily reminder push notification
4. **Challenge 4**: Achieve 100/100 on Lighthouse PWA audit
5. **Challenge 5**: Successfully install and test on Android, iOS, and Desktop

---

## Technical Requirements

### For Participants:
- Laptop with Docker and Docker Compose
- Modern browser (Chrome/Edge recommended for DevTools)
- Text editor/IDE
- Mobile device for testing (optional but recommended)
- Basic terminal/command line knowledge

### For the Application:
- Symfony 7.3+
- PHP 8.2+
- HTTPS (required for PWA features)
- Modern browser with PWA support

---

## Expected Outcomes

By the end of the workshop, FieldLog will:
- Be installable as a standalone app on mobile and desktop
- Function completely offline (viewing and creating entries)
- Receive shared content from other applications
- Send push notifications to users
- Have a Lighthouse PWA score > 90
- Use Service Worker with multiple caching strategies
- Handle background synchronization
- Integrate with system-level APIs

---

## File Structure (Post-Workshop)

```
config/
└── packages/
    └── pwa.yaml                    # PWA Bundle configuration

public/
├── manifest.json                   # Generated Web App Manifest
├── serviceworker.js                # Generated Service Worker
└── icons/                          # App icons
    ├── icon-192.png
    ├── icon-512.png
    └── icon-maskable.png

assets/
└── pwa/                            # Custom PWA scripts
    ├── install-prompt.js           # Installation flow
    ├── update-checker.js           # SW update detection
    ├── offline-handler.js          # Offline state management
    └── sync-manager.js             # Background sync logic

templates/
└── pwa/
    └── offline.html.twig           # Offline fallback page

src/
└── Controller/
    └── ShareTargetController.php   # Handle shared content
```

---

## Key Concepts Summary

- **Progressive Enhancement**: App works for everyone, enhanced for capable browsers
- **Service Worker**: JavaScript proxy between app and network
- **Manifest**: JSON file describing app metadata and appearance
- **Offline-First**: Design pattern prioritizing offline functionality
- **Background Sync**: Defer actions until connectivity is restored
- **Push Notifications**: Re-engage users with timely updates
- **Share Target**: Make your app a destination for shared content

---

## Resources & References

- [Spomky-Labs/PWA-Bundle Documentation](https://github.com/Spomky-Labs/pwa-bundle)
- [MDN PWA Guide](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
- [Web.dev PWA](https://web.dev/progressive-web-apps/)
- [Service Worker API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [Workbox (Google's SW Library)](https://developers.google.com/web/tools/workbox)
- [PWA Builder](https://www.pwabuilder.com/)

---

## Post-Workshop

After completing this workshop, consider exploring:
- **IndexedDB** for complex offline data storage
- **Web Workers** for background computations
- **WebRTC** for peer-to-peer communication
- **WebAssembly** for performance-critical features
- **Advanced caching strategies** with Workbox
- **App Store deployment** (Microsoft Store, Google Play)
