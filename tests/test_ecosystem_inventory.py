import json
import pathlib
import unittest

ROOT = pathlib.Path(__file__).resolve().parents[1]


class EcosystemInventoryTests(unittest.TestCase):
    def test_inventory_has_35_categories(self):
        content = (ROOT / "docs" / "GITHUB_AZ_ECOSYSTEM.md").read_text(encoding="utf-8")
        self.assertIn("35. Full documentation", content)
        self.assertIn("1. Web development", content)

    def test_project_map_parses_and_has_categories(self):
        data = json.loads((ROOT / "project-map.json").read_text(encoding="utf-8"))
        self.assertEqual(len(data["categories"]), 35)
        self.assertFalse(data["policy"]["production_deployment_enabled"])

    def test_security_checklist_exists(self):
        self.assertTrue((ROOT / "docs" / "SECURITY_CHECKLIST.md").is_file())


if __name__ == "__main__":
    unittest.main()
