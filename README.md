# RIP — REST In Peace

> Lightweight execution engine for WordPress & WooCommerce REST APIs.

RIP reduces WordPress overhead for REST API requests by preventing unnecessary plugins and hooks from loading during API execution.

Instead of optimizing responses after WordPress fully boots, RIP optimizes the **execution layer itself**.

---

# Why RIP Exists

A normal WooCommerce REST request usually loads:

- all active plugins
- page builders
- SEO plugins
- frontend hooks
- analytics
- tracking scripts
- unnecessary WooCommerce components

Even simple API requests can trigger massive overhead.

RIP intercepts REST requests early and disables unnecessary components before they load.

---

# Current Features (v1)

- MU-plugin architecture
- Early REST request detection
- Per-endpoint execution profiles
- Plugin unloading
- Hook stripping
- WooCommerce slim mode
- Safe mode bypass
- Minimal and lightweight

---

# Example

For this endpoint:

```text
/wp-json/wc/v3/products
```

RIP can disable:

- Elementor
- Elementor Pro
- SEO plugins
- analytics plugins
- tracking plugins
- admin/debug plugins
- frontend-only plugins

before WordPress fully initializes.

---

# Installation

## 1. Copy files

Upload:

```text
mu-plugins/
```

to:

```text
wp-content/mu-plugins/
```

Final structure:

```text
wp-content/
└── mu-plugins/
    ├── rip-bootstrap.php
    └── rip/
```

---

## 2. Done

RIP activates automatically because it runs as a WordPress MU-plugin.

No activation required.

---

# Safe Mode

To temporarily disable RIP for a request:

```text
?rip_safe=1
```

Example:

```text
https://example.com/wp-json/wc/v3/products?rip_safe=1
```

---

# Current Scope

RIP currently targets:

- WooCommerce REST API
- WordPress REST API
- Read-heavy endpoints

It does NOT currently optimize:

- checkout
- cart
- wp-admin
- frontend rendering

---

# Goals

Future RIP versions may include:

- adaptive plugin learning
- binary plugin isolation
- Redis-aware response caching
- endpoint auto-profiling
- fast-path execution
- partial WordPress bypassing

---

# Philosophy

Most optimization plugins focus on:

- caching
- frontend assets
- database queries

RIP focuses on:

> execution reduction

The fastest code is the code that never runs.

---

# Compatibility

RIP is intentionally conservative in v1.

Always test endpoints before production deployment.

---

# License

MIT License

---

# Author

Created by Mohsen Ghiasi

Project:
https://github.com/mohsengham/rip-wordpress
