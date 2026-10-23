import pathlib
import unittest

ROOT = pathlib.Path(__file__).resolve().parents[1]


class ProjectBlueprintTests(unittest.TestCase):
    def test_operations_document_exists(self):
        self.assertTrue((ROOT / "docs" / "OPERATIONS.md").is_file())

    def test_project_blueprint_mentions_safety(self):
        text = (ROOT / "docs" / "PROJECT_BLUEPRINT.md").read_text(encoding="utf-8")
        self.assertIn("Human approval", text)
        self.assertIn("Security", text)


if __name__ == "__main__":
    unittest.main()
