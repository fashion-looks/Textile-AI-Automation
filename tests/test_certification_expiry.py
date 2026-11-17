import unittest
from datetime import date

from ecosystem.certification_expiry import (
    assess_certificate,
    assess_certificate_batch,
)


class CertificationExpiryTests(unittest.TestCase):
    def setUp(self):
        self.today = date(2026, 11, 17)
        self.certificate = {
            "company": "Fashion Looks Textiles",
            "standard": "GOTS",
            "certificate_number": "GOTS-IND-2026-001",
            "issue_date": "2026-01-01",
            "expiry_date": "2027-01-01",
        }

    def test_classifies_certificate_outside_warning_window_as_valid(self):
        result = assess_certificate(
            self.certificate, today=self.today, warning_days=30
        )
        self.assertEqual(result["status"], "valid")
        self.assertEqual(result["days_to_expiry"], 45)

    def test_classifies_certificate_inside_warning_window(self):
        result = assess_certificate(
            {**self.certificate, "expiry_date": "2026-12-17"},
            today=self.today,
            warning_days=30,
        )
        self.assertEqual(result["status"], "expiring_soon")
        self.assertEqual(result["days_to_expiry"], 30)

    def test_expiry_today_is_expiring_soon_not_expired(self):
        result = assess_certificate(
            {**self.certificate, "expiry_date": "2026-11-17"},
            today=self.today,
        )
        self.assertEqual(result["status"], "expiring_soon")
        self.assertEqual(result["days_to_expiry"], 0)

    def test_classifies_past_expiry_as_expired(self):
        result = assess_certificate(
            {**self.certificate, "expiry_date": "2026-11-16"},
            today=self.today,
        )
        self.assertEqual(result["status"], "expired")
        self.assertEqual(result["days_to_expiry"], -1)

    def test_rejects_future_issue_date(self):
        with self.assertRaisesRegex(ValueError, "issue_date cannot be in the future"):
            assess_certificate(
                {**self.certificate, "issue_date": "2026-11-18"},
                today=self.today,
            )

    def test_rejects_expiry_before_issue_date(self):
        with self.assertRaisesRegex(ValueError, "expiry_date cannot be before"):
            assess_certificate(
                {
                    **self.certificate,
                    "issue_date": "2026-10-01",
                    "expiry_date": "2026-09-30",
                },
                today=self.today,
            )

    def test_rejects_non_iso_date(self):
        with self.assertRaisesRegex(ValueError, "expiry_date must use YYYY-MM-DD"):
            assess_certificate(
                {**self.certificate, "expiry_date": "17/11/2026"},
                today=self.today,
            )

    def test_batch_preserves_order_and_reports_invalid_rows(self):
        payload = {
            "warning_days": 30,
            "certificates": [
                self.certificate,
                {**self.certificate, "certificate_number": "GOTS-002",
                 "expiry_date": "2026-11-01"},
                {"company": "Missing fields"},
            ],
        }
        result = assess_certificate_batch(payload, today=self.today)
        self.assertEqual(result["total"], 3)
        self.assertEqual(result["valid"], 2)
        self.assertEqual(result["invalid"], 1)
        self.assertEqual(result["summary"]["valid"], 1)
        self.assertEqual(result["summary"]["expired"], 1)
        self.assertEqual(result["results"][2]["status"], "invalid")
        self.assertEqual([row["index"] for row in result["results"]], [0, 1, 2])

    def test_rejects_warning_window_outside_supported_range(self):
        for value in (-1, 366, True, 30.5):
            with self.subTest(value=value):
                with self.assertRaises(ValueError):
                    assess_certificate_batch({
                        "warning_days": value,
                        "certificates": [self.certificate],
                    }, today=self.today)

    def test_rejects_empty_and_oversized_batches(self):
        for certificates in ([], [self.certificate] * 51):
            with self.subTest(count=len(certificates)):
                with self.assertRaises(ValueError):
                    assess_certificate_batch({
                        "certificates": certificates,
                    }, today=self.today)


if __name__ == "__main__":
    unittest.main()
