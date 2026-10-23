"""Inventory of the 35 development categories and their maturity."""
CATEGORIES = [
    (1, "Web development", "HTML, CSS, JavaScript, responsive UI"),
    (2, "Mobile apps", "Android/iOS/Flutter/React Native"),
    (3, "Backend", "Python APIs and business logic"),
    (4, "Databases", "SQLite prototype; production DB adapter planned"),
    (5, "AI/ML", "Provider-neutral AI interface; credentials required"),
    (6, "Data analysis", "CSV summaries and data validation"),
    (7, "Automation", "Bounded jobs and GitHub Actions"),
    (8, "Web scraping", "Permission-based collection only"),
    (9, "APIs", "Typed JSON endpoints and input validation"),
    (10, "E-commerce", "Product catalog and order model"),
    (11, "DevOps", "Build and test pipelines"),
    (12, "Cloud hosting", "Deployment guide; provider configuration required"),
    (13, "Version control", "Git workflow documentation"),
    (14, "Testing", "Unit and API tests"),
    (15, "Security", "Safe defaults and security checklist"),
    (16, "Messaging bots", "Adapter interface; credentials and consent required"),
    (17, "Email automation", "Draft generation only; no sending by default"),
    (18, "Scheduling", "Manual and scheduled job entry points"),
    (19, "Monitoring", "Health endpoint and structured logs"),
    (20, "File management", "CSV import/export with validation"),
    (21, "Communication", "Notification adapter interface"),
    (22, "Payments", "Sandbox integration interface only"),
    (23, "Maps", "Location adapter interface; provider key required"),
    (24, "IoT/hardware", "Device simulator and payload validation"),
    (25, "Game development", "Small browser game demo"),
    (26, "Blockchain/Web3", "Read-only concept adapter; no wallet operations"),
    (27, "Desktop apps", "Desktop packaging guide"),
    (28, "Data visualization", "Dashboard summary endpoint"),
    (29, "UI/UX", "Responsive single-page interface"),
    (30, "Documentation", "Setup and operations docs"),
    (31, "Project templates", "Reusable project skeleton"),
    (32, "Boilerplates", "Minimal runnable examples"),
    (33, "CI/CD", "Automated checks on push and pull request"),
    (34, "Deployment scripts", "Non-production validation scripts"),
    (35, "Full documentation", "A-Z inventory and maturity reporting"),
]

def inventory():
    return [
        {"id": number, "name": name, "description": description,
         "status": "starter" if number in {1, 3, 4, 6, 7, 9, 13, 14, 15, 18, 19, 20, 28, 29, 30, 31, 32, 33, 35} else "adapter-needed"}
        for number, name, description in CATEGORIES
    ]
