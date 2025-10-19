# PWA Workshop - Facilitator Guide

## Overview

This guide provides all the information you need as a workshop facilitator to successfully deliver the PWA workshop using FieldLog and the Spomky-Labs/PWA-Bundle.

**Workshop Duration**: 6-8 hours (1 full day)
**Target Audience**: Intermediate web developers with Symfony experience
**Format**: Hands-on coding workshop with theoretical concepts

---

## Your Role as Facilitator

### Before the Workshop
1. **Prepare the environment** (see Preparation Checklist)
2. **Test all exercises** on multiple devices
3. **Prepare demo instances** of each stage
4. **Review common issues** and solutions
5. **Set up testing infrastructure** (HTTPS, mobile devices)

### During the Workshop
1. **Balance theory and practice** (20% theory, 80% practice)
2. **Adapt pace** based on participant progress
3. **Encourage experimentation** and questions
4. **Circulate during exercises** to provide individual help
5. **Share real-world experiences** and use cases
6. **Keep energy high** with breaks and challenges

### Key Teaching Points
- Emphasize **progressive enhancement** philosophy
- Explain **why**, not just **how**
- Connect concepts to **real-world applications**
- Highlight **common pitfalls** and best practices
- Demonstrate on **multiple devices** when possible

---

## Workshop Flow & Timing

### Recommended Schedule

#### Morning (9:00 - 12:30)

| Time | Duration | Activity | Your Role |
|------|----------|----------|-----------|
| 9:00 - 9:30 | 30 min | Introduction & Setup | Present PWA concepts, help with bundle installation |
| 9:30 - 10:30 | 60 min | Web App Manifest | Guide manifest creation, explain each property, demo installation |
| 10:30 - 10:45 | 15 min | **BREAK** | Check progress, troubleshoot individual issues |
| 10:45 - 12:15 | 90 min | Service Worker Basics | Explain lifecycle carefully, demo DevTools, help with strategies |
| 12:15 - 12:30 | 15 min | Offline Mode Setup | Quick demo, assign as lunch exercise |

#### Lunch (12:30 - 13:30)
- Participants can test offline mode
- Available for questions
- Prepare afternoon demos

#### Afternoon (13:30 - 17:30)

| Time | Duration | Activity | Your Role |
|------|----------|----------|-----------|
| 13:30 - 15:00 | 90 min | Background Sync | Explain queue concept, demo network throttling, help debug |
| 15:00 - 15:15 | 15 min | **BREAK** | Check progress, help with sync issues |
| 15:15 - 16:15 | 60 min | Push Notifications | Demo VAPID setup, explain permissions UX, test notifications |
| 16:15 - 17:15 | 60 min | System Integration | Quick overview of APIs, participants choose features to implement |
| 17:15 - 17:30 | 15 min | Optimization & Wrap-up | Run Lighthouse audits, Q&A, next steps |

**Flexibility Notes:**
- Background Sync may take longer - it's complex
- Push Notifications can be shortened if time runs short
- System Integration features can be optional/homework
- Budget 10-15 min buffer for each section

---

## Preparation Timeline

### 2 Weeks Before
- [ ] Review FieldLog codebase thoroughly
- [ ] Test complete workshop flow yourself
- [ ] Prepare all demo checkpoints (git branches recommended)
- [ ] Set up SSL certificates for testing environment
- [ ] Order/prepare test devices (Android, iOS if possible)
- [ ] Create participant list and send prerequisites

### 1 Week Before
- [ ] Send setup instructions to participants
- [ ] Test on multiple browsers and devices
- [ ] Prepare backup solutions for common issues
- [ ] Set up screen sharing/projection equipment
- [ ] Create Slack/Discord for workshop communication
- [ ] Prepare slides for theoretical sections

### 1 Day Before
- [ ] Verify all URLs and services work
- [ ] Test HTTPS access from different networks
- [ ] Prepare WiFi access information
- [ ] Print preparation checklist for participants
- [ ] Charge all test devices
- [ ] Prepare coffee/snack schedule

### Morning Of
- [ ] Arrive 30 minutes early
- [ ] Test projector/screen sharing
- [ ] Verify WiFi works
- [ ] Test HTTPS access
- [ ] Set up demo devices at front of room
- [ ] Write WiFi credentials on board

---

## Teaching Strategies

### For Each Section

1. **Start with "Why"**
   - Explain the problem this feature solves
   - Show real-world examples
   - Connect to FieldLog's use case

2. **Demo First**
   - Show the end result working
   - Let participants see what they're building
   - Use multiple devices when relevant

3. **Explain Concepts**
   - Keep theory brief (5-10 minutes max)
   - Use diagrams on whiteboard/slides
   - Relate to web development concepts they know

4. **Guided Implementation**
   - Walk through first steps together
   - Show bundle configuration clearly
   - Explain each line of code

5. **Independent Practice**
   - Give clear objectives
   - Circulate to help individuals
   - Encourage peer helping

6. **Test & Validate**
   - Show DevTools debugging together
   - Test on real devices
   - Celebrate small wins

### Handling Different Skill Levels

**For Advanced Participants:**
- Provide extension challenges
- Ask them to help neighbors
- Suggest advanced topics to explore

**For Struggling Participants:**
- Provide code snippets to copy
- Pair with stronger participants
- Focus on getting something working first
- Offer to help during breaks

**For Everyone:**
- Emphasize that PWA is complex
- It's okay not to understand everything immediately
- Focus on building working features

---

## Key Demonstrations

### Critical Demos You Must Prepare

1. **App Installation Flow**
   - Desktop Chrome (show install prompt)
   - Android (show banner, install, app drawer)
   - iOS (show Add to Home Screen)
   - Show installed app behavior vs browser

2. **Service Worker Lifecycle**
   - Chrome DevTools Application tab
   - Show install → activate → fetch events
   - Demo update flow
   - Show cache inspection

3. **Offline Mode**
   - Enable network throttling (Offline)
   - Show fallback page
   - Show cached content working
   - Demonstrate sync when back online

4. **Background Sync**
   - Create entry while offline
   - Show pending state
   - Reconnect and watch sync
   - Inspect sync events in DevTools

5. **Push Notification**
   - Send from server
   - Show on locked device
   - Demo click action
   - Show notification center

6. **Share Target**
   - Share from another app
   - Show FieldLog appear in share sheet
   - Receive and process content

### Demo Equipment Setup

- **Primary Screen**: Your code editor + browser
- **Secondary Screen**: Test device screens (via USB debugging or screen mirror)
- **Physical Devices**: Pass around for participants to see
- **Network Throttling**: Always visible in DevTools during offline demos

---

## Common Questions & Answers

### Technical Questions

**Q: "Why HTTPS? Can't we test locally?"**
A: Service Workers require HTTPS for security. Localhost is exempt, but you need HTTPS for device testing. Explain the security implications.

**Q: "What happens if the Service Worker has a bug?"**
A: Show how to unregister in DevTools. Explain SW update mechanism. Emphasize the importance of versioning and testing.

**Q: "Can we use IndexedDB instead of Background Sync?"**
A: Yes, but Background Sync handles the network retry automatically. IndexedDB is great for storage, but you need to implement sync logic yourself.

**Q: "Do all browsers support PWA?"**
A: Show [caniuse.com](https://caniuse.com) for each API. Emphasize progressive enhancement - the app should work everywhere, enhanced where supported.

**Q: "How do updates work for installed apps?"**
A: Service Worker checks for updates on navigation. You can implement update prompts. Demo the update lifecycle.

### Conceptual Questions

**Q: "When should I use PWA vs Native app?"**
A: Discuss trade-offs:
- PWA: Easier updates, cross-platform, web skills, limited hardware access
- Native: Better performance, full hardware access, app store presence, separate codebases

**Q: "Is PWA just for mobile?"**
A: No! Desktop PWA is growing. Show desktop installation and integration. Many productivity apps work great as desktop PWAs.

**Q: "What about SEO?"**
A: PWA doesn't hurt SEO - it's still a website. Service Worker can actually improve performance scores, which helps SEO.

### Bundle-Specific Questions

**Q: "Why use this bundle vs vanilla Service Worker?"**
A: Bundle integrates with Symfony ecosystem, handles precaching, generates manifest, provides Symfony-friendly APIs. Saves a lot of boilerplate.

**Q: "Can we customize the generated Service Worker?"**
A: Yes, the bundle provides hooks and configuration. Show config options in pwa.yaml.

**Q: "How does this bundle handle different environments?"**
A: It's environment-aware. Show dev vs prod configs. Service Worker behavior can differ.

---

## Troubleshooting Common Issues

### Installation Problems

**Issue**: Bundle doesn't install
- Check Symfony version compatibility
- Run `composer clear-cache`
- Try `composer require spomky-labs/pwa-bundle --with-all-dependencies`

**Issue**: Configuration not recognized
- Clear Symfony cache: `php bin/console cache:clear`
- Check YAML syntax
- Verify bundle is in config/bundles.php

### Service Worker Issues

**Issue**: Service Worker not registering
- Verify HTTPS (or localhost)
- Check browser console for errors
- Ensure serviceworker.js is accessible
- Check for JavaScript errors preventing registration

**Issue**: Service Worker not updating
- Click "Update" in DevTools
- Unregister and reload
- Check Update on Reload in DevTools
- Clear browser cache completely

**Issue**: Caching not working
- Inspect Cache Storage in DevTools
- Check network tab for cache hits
- Verify cache strategy configuration
- Check Service Worker fetch events

### Manifest Issues

**Issue**: App not installable
- Run Lighthouse PWA audit
- Check manifest URL is correct
- Verify icons exist and are correct format
- Ensure HTTPS
- Check for manifest errors in console

**Issue**: Icons not showing
- Verify icon paths are correct
- Check icon file sizes match manifest
- Try PNG format (most compatible)
- Clear browser cache

### Offline Issues

**Issue**: Offline page not showing
- Check fallback is properly cached
- Verify network strategy
- Test with DevTools offline mode
- Check Service Worker fetch event handler

**Issue**: Some resources fail offline
- Inspect Cache Storage
- Verify precache list includes all dependencies
- Check for absolute vs relative URLs
- Ensure all assets are from same origin

### Background Sync Issues

**Issue**: Sync not triggering
- Check if browser supports Background Sync
- Verify sync registration in DevTools (Background Services)
- Test with network throttling, not full offline
- Check sync event handler

**Issue**: Sync data lost
- Verify IndexedDB or localStorage persistence
- Check for browser clearing data
- Implement proper error handling
- Log sync attempts for debugging

### Push Notification Issues

**Issue**: Permission denied
- Explain permissions must be requested on user action
- Show proper UX for requesting permission
- Check browser settings
- Test in incognito (fresh permission state)

**Issue**: Notifications not received
- Verify VAPID keys are correct
- Check subscription is stored server-side
- Test with browser push testing tools
- Verify notification service is running

### Device-Specific Issues

**Issue**: iOS not installing
- Explain iOS has limited PWA support
- Show manual "Add to Home Screen"
- Note some features don't work on iOS
- Demonstrate what does work

**Issue**: Android install banner not showing
- Check manifest criteria
- Verify engagement heuristics (visits, time)
- Use `beforeinstallprompt` event
- Test on Chrome/Edge Android

---

## Extension Activities

If participants finish early or want extra challenges:

### Advanced Challenges
1. **IndexedDB Integration**: Store entries locally in IndexedDB
2. **Periodic Background Sync**: Check for new content periodically
3. **Advanced Caching**: Implement cache expiration strategies
4. **App Shortcuts**: Create dynamic shortcuts based on recent entries
5. **File Handling**: Register FieldLog as handler for specific file types
6. **Badging**: Show unread count badge on app icon
7. **Screenshots**: Add app screenshots to manifest for app stores
8. **Categories**: Add categories to manifest for better discovery

### Extra Features for FieldLog
1. **Offline Images**: Implement smart image caching
2. **Search Offline**: Enable search in cached entries
3. **Conflict Resolution**: Handle sync conflicts when same entry edited offline/online
4. **Progressive Loading**: Load thumbnails first, full images later
5. **Export/Import**: Export journal to JSON, import from backup

### Deep Dives
1. **Workbox**: Introduce Google's Service Worker library
2. **Testing**: Unit test Service Worker logic
3. **Performance**: Advanced performance optimization
4. **Analytics**: Track offline usage
5. **A/B Testing**: Test different caching strategies

---

## Facilitator Self-Care

### Energy Management
- Stay hydrated - keep water at podium
- Take breaks yourself during participant exercises
- Don't skip lunch
- Stand and move around while teaching
- Take a real break during participant lunch

### Managing Stress
- It's okay if things go slightly off schedule
- Not everyone will finish everything - that's normal
- Have backup plans for technical failures
- Ask co-facilitators for help if available
- Remember: participants learn from problems too

### After the Workshop
- Collect feedback (written survey)
- Note what worked well and what didn't
- Update this guide with learnings
- Rest - teaching is exhausting!

---

## Workshop Success Metrics

By the end, participants should have:
- [ ] Installed the PWA bundle
- [ ] Created a working manifest
- [ ] Implemented a Service Worker with at least 2 caching strategies
- [ ] Made FieldLog work offline
- [ ] Installed the app on at least one device
- [ ] Implemented at least one advanced feature (sync or notifications)
- [ ] Achieved a Lighthouse PWA score > 70

Success markers during workshop:
- Participants asking thoughtful questions
- Peer-to-peer helping happening
- "Aha!" moments and excitement
- Apps installing successfully
- Positive energy in the room

---

## Contact & Support

### During Workshop
- Create a dedicated Slack/Discord channel
- Share your contact method for quick questions
- Establish office hours for post-workshop help

### After Workshop
- Provide email for follow-up questions
- Share additional resources and readings
- Consider a follow-up session in 2-4 weeks
- Create a community for participants to share progress

---

## Additional Resources for You

### Technical Deep Dives
- [Service Worker Specification](https://w3c.github.io/ServiceWorker/)
- [Spomky-Labs Bundle GitHub](https://github.com/Spomky-Labs/pwa-bundle)
- [Workbox Documentation](https://developers.google.com/web/tools/workbox)
- [Jake Archibald's Blog](https://jakearchibald.com/) - PWA expert

### Teaching Resources
- [web.dev PWA Patterns](https://web.dev/patterns/web-app-manifest/)
- [PWA Workshop Examples](https://github.com/GoogleChrome/samples/tree/gh-pages/service-worker)
- [Mozilla PWA Tutorials](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)

### Tools
- [PWA Builder](https://www.pwabuilder.com/) - Test and validate PWAs
- [Lighthouse CI](https://github.com/GoogleChrome/lighthouse-ci) - Automate audits
- [Workbox Window](https://developers.google.com/web/tools/workbox/modules/workbox-window) - SW lifecycle in page

---

## Updates & Maintenance

This guide should be updated:
- After each workshop delivery
- When Spomky-Labs bundle updates
- When browser APIs change
- When new best practices emerge

Keep a changelog of your updates to track improvements over time.

---

Good luck with your workshop! Remember: your enthusiasm and real-world experience are what make this workshop valuable. Technical knowledge can be found in docs, but your insights make it memorable.
