-- Mora Hiking Blog Database Schema
-- University of Moratuwa Hiking Club

--CREATE DATABASE IF NOT EXISTS `mora_hiking_blog` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
--USE `mora_hiking_blog`;

-- Drop existing tables if needed
DROP TABLE IF EXISTS `blogPost`;
DROP TABLE IF EXISTS `user`;

-- 1. User Table
CREATE TABLE `user` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('member', 'admin') DEFAULT 'member',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Blog Post Table
CREATE TABLE `blogPost` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_blog_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Seed Sample Users
-- Passwords for both sample accounts is: Password@123
-- Hash generated using PASSWORD_BCRYPT
INSERT INTO `user` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'mora_admin', 'admin@hiking.mrt.ac.lk', '$2y$10$ENsFI0V6T9BFpOL.N9PyA.bx01fPl4jp6ZJ8Y89e6x6urs1u4kWVe', 'admin', NOW()),
(2, 'chathura_k', 'chathura@mora.ac.lk', '$2y$10$ENsFI0V6T9BFpOL.N9PyA.bx01fPl4jp6ZJ8Y89e6x6urs1u4kWVe', 'member', NOW()),
(3, 'rashmi_w', 'rashmi@mora.ac.lk', '$2y$10$ENsFI0V6T9BFpOL.N9PyA.bx01fPl4jp6ZJ8Y89e6x6urs1u4kWVe', 'member', NOW());

-- 4. Seed Sample Blog Posts (Markdown formatted)
INSERT INTO `blogPost` (`id`, `user_id`, `title`, `content`, `created_at`, `updated_at`) VALUES
(1, 1, 'Expedition Report: Conquering the Hanthana Seven Peaks', 
'# Hanthana Mountain Range Expedition 🌲

The **Hanthana Range** in Kandy is one of the most iconic trekking spots for university hiking clubs in Sri Lanka. Last weekend, 18 members of the **Mora Hiking Club** embarked on an early morning hike to conquer all seven peaks.

---

### Trail Summary
- **Total Distance:** 12.4 km
- **Elevation Gain:** 820 m
- **Difficulty:** Moderate
- **Duration:** 6.5 hours

### Highlights of the Journey
1. **The Misty Ascent:** We started from the Sarasavi Medura entrance around 6:00 AM as the cold morning mist shrouded the eucalyptus trees.
2. **Peak 4 Panorama:** At the Fourth Peak, the entire valley of Peradeniya and the Mahaweli River unfolded beneath us.
3. **Biodiversity:** We spotted multiple endemic bird species including the *Sri Lanka Junglefowl* and various highland orchids.

> "Between every two pines is a doorway to a new world." — John Muir

### Essential Gear Checklist for Hanthana
- [x] Sturdy trail shoes with aggressive grip (grass slopes can be slippery)
- [x] Leech socks and citronella spray
- [x] At least 2.5L water per person
- [x] Rain poncho / lightweight dry bag

We concluded the trek at the fourth milestone on Galaha Road, tired but energized by the spirit of brotherhood and nature.', 
NOW() - INTERVAL 4 DAY, NOW() - INTERVAL 4 DAY),

(2, 2, 'Devil\'s Staircase Trek & Camping at Bambarakanda', 
'# Devil\'s Staircase: The Wild Trail of Ohiya ⛰️

Connecting Ohiya to Kalupahana, the **Devil\'s Staircase** trail is a bucket-list expedition for serious Sri Lankan trekkers. Our Mora crew tackled this 14 km downhill and steep gradient trek under clear sunny skies.

---

### Key Waypoints
* **Ohiya Railway Station:** Starting point at 1,820m elevation.
* **V-Cut:** Dramatic natural rock gap carved for the estate road.
* **Lover\'s Leap Waterfall:** A serene stop for quick hydration and trail snacks.
* **Bambarakanda Upper Falls:** Sri Lanka\'s highest waterfall (263m) plunge pool.

### Camping Experience
We pitched our tents at the Bambarakanda campsite. As the sun dipped behind the tea estates, the golden hue across the valley was absolutely breathtaking. We brewed hot Ceylon tea, shared trail stories, and star-gazed until midnight.

```
Trail Note: The descent from the V-Cut has loose gravel; maintain 3-point contact when walking near the precipice!
```

Stay tuned for our upcoming trek to Knuckles Mountain Range!', 
NOW() - INTERVAL 2 DAY, NOW() - INTERVAL 2 DAY),

(3, 3, 'Beginner\'s Guide: Top 5 Packing Essentials for Mora Hikers', 
'# Gear Up: 5 Essentials Every Mora Hiker Must Carry 🎒

Whether you are joining our club for your very first day hike to **Bathalegala (Bible Rock)** or preparing for an overnight survival camp, packing smart is key to safety and comfort.

---

### 1. Hydration & Electrolytes
Dehydration is the #1 reason hikers experience cramps and exhaustion. Carry a reusable water bottle or a hydration bladder (2L minimum).

### 2. Reliable Navigation & Emergency Lighting
Always carry a fully charged power bank, offline maps (Maps.me / OSMAnd), and a **headlamp** with spare batteries.

### 3. First Aid Kit
Your personal kit should include:
- Bandages & antiseptic wipes
- Antihistamines & pain relievers
- Leech repellent (Dettol / salt spray)
- ORS (Oral Rehydration Salts)

### 4. Layering System
Sri Lankan hill country weather changes in minutes:
- Base layer: Moisture-wicking synthetic t-shirt (avoid heavy cotton)
- Mid layer: Light fleece
- Outer layer: Breathable windproof/waterproof jacket

### 5. Leave No Trace Principle 🍃
**Carry a garbage trash bag.** Whatever you pack in, pack it all out. Keep our beautiful island pristine!', 
NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY);
