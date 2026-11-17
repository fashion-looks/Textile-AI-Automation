import unittest
from datetime import date

from ecosystem.purchase_orders import assess_purchase_order, assess_purchase_order_batch


class PurchaseOrderAssessmentTests(unittest.TestCase):
    def setUp(self):
        self.payload = {
            "po_number": "FL-PO-1001",
            "supplier": "Example Textile Mill",
            "buyer": "Global Buyer",
            "style": "TS-01",
            "fabric": "Cotton Jersey",
            "order_qty": 10000,
            "received_qty": 6250.5,
            "committed_delivery_date": "2026-11-20",
        }

    def test_calculates_balance_and_in_progress_status(self):
        result = assess_purchase_order(self.payload, today=date(2026, 11, 17))
        self.assertEqual(result["balance_qty"], 3749.5)
        self.assertEqual(result["overdue_days"], 0)
        self.assertEqual(result["status"], "in_progress")

    def test_calculates_overdue_days_from_committed_date(self):
        result = assess_purchase_order(self.payload, today=date(2026, 11, 23))
        self.assertEqual(result["overdue_days"], 3)
        self.assertEqual(result["status"], "overdue")

    def test_completed_order_is_not_overdue(self):
        payload = {**self.payload, "received_qty": 10000}
        result = assess_purchase_order(payload, today=date(2026, 12, 1))
        self.assertEqual(result["balance_qty"], 0)
        self.assertEqual(result["overdue_days"], 0)
        self.assertEqual(result["status"], "complete")

    def test_due_today_is_distinct_from_overdue(self):
        result = assess_purchase_order(self.payload, today=date(2026, 11, 20))
        self.assertEqual(result["overdue_days"], 0)
        self.assertEqual(result["status"], "due_today")

    def test_rejects_received_quantity_above_order_quantity(self):
        payload = {**self.payload, "received_qty": 10001}
        with self.assertRaisesRegex(ValueError, "cannot exceed"):
            assess_purchase_order(payload, today=date(2026, 11, 17))

    def test_rejects_negative_quantity(self):
        payload = {**self.payload, "received_qty": -1}
        with self.assertRaisesRegex(ValueError, "between 0"):
            assess_purchase_order(payload, today=date(2026, 11, 17))

    def test_rejects_more_than_three_decimal_places(self):
        payload = {**self.payload, "received_qty": 1.2345}
        with self.assertRaisesRegex(ValueError, "3 decimal places"):
            assess_purchase_order(payload, today=date(2026, 11, 17))

    def test_rejects_invalid_delivery_date(self):
        payload = {**self.payload, "committed_delivery_date": "20/11/2026"}
        with self.assertRaisesRegex(ValueError, "YYYY-MM-DD"):
            assess_purchase_order(payload, today=date(2026, 11, 17))

    def test_rejects_non_object_payload(self):
        with self.assertRaisesRegex(ValueError, "JSON object"):
            assess_purchase_order([], today=date(2026, 11, 17))

    def test_batch_preserves_order_and_summarizes_mixed_rows(self):
        payload = {
            "purchase_orders": [
                self.payload,
                {**self.payload, "po_number": "FL-PO-INVALID", "received_qty": 10001},
                {**self.payload, "po_number": "FL-PO-OVERDUE"},
            ]
        }
        result = assess_purchase_order_batch(payload, today=date(2026, 11, 23))
        self.assertEqual(result["total"], 3)
        self.assertEqual(result["valid"], 2)
        self.assertEqual(result["invalid"], 1)
        self.assertEqual(result["results"][0]["index"], 0)
        self.assertEqual(result["results"][1]["status"], "invalid")
        self.assertEqual(result["summary"]["overdue"], 2)

    def test_batch_rejects_missing_empty_and_too_many_orders(self):
        for payload in (
            {},
            {"purchase_orders": []},
            {"purchase_orders": [{}] * 51},
        ):
            with self.subTest(payload_keys=list(payload)):
                with self.assertRaises(ValueError):
                    assess_purchase_order_batch(payload, today=date(2026, 11, 17))


if __name__ == "__main__":
    unittest.main()
