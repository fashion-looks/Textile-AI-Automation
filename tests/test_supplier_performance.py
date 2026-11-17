import unittest
from datetime import date

from ecosystem.supplier_performance import summarize_supplier_performance


class SupplierPerformanceTests(unittest.TestCase):
    def setUp(self):
        self.orders = [
            {
                "po_number": "FL-1001",
                "supplier": "Mill A",
                "order_qty": 1000,
                "received_qty": 1000,
                "committed_delivery_date": "2026-11-20",
                "actual_delivery_date": "2026-11-19",
            },
            {
                "po_number": "FL-1002",
                "supplier": "Mill A",
                "order_qty": 1000,
                "received_qty": 1000,
                "committed_delivery_date": "2026-11-20",
                "actual_delivery_date": "2026-11-23",
            },
            {
                "po_number": "FL-1003",
                "supplier": "Mill A",
                "order_qty": 500,
                "received_qty": 250,
                "committed_delivery_date": "2026-11-22",
            },
            {
                "po_number": "FL-1004",
                "supplier": "Mill B",
                "order_qty": 200,
                "received_qty": 200,
                "committed_delivery_date": "2026-11-23",
                "actual_delivery_date": "2026-11-23",
            },
        ]

    def test_groups_suppliers_and_calculates_delivery_metrics(self):
        report = summarize_supplier_performance(
            {"purchase_orders": self.orders},
            today=date(2026, 11, 23),
        )
        self.assertEqual(report["total_purchase_orders"], 4)
        self.assertEqual(report["total_suppliers"], 2)
        mill_a, mill_b = report["suppliers"]
        self.assertEqual(mill_a["supplier"], "Mill A")
        self.assertEqual(mill_a["on_time_delivery_rate_pct"], 50.0)
        self.assertEqual(mill_a["average_late_days"], 3.0)
        self.assertEqual(mill_a["overdue_open_pos"], 1)
        self.assertEqual(mill_a["balance_qty"], 250.0)
        self.assertEqual(mill_a["completion_rate_pct"], 66.67)
        self.assertEqual(mill_b["on_time_delivery_rate_pct"], 100.0)

    def test_completed_order_without_actual_date_is_unverified(self):
        order = {
            "po_number": "FL-2001",
            "supplier": "Mill C",
            "order_qty": 100,
            "received_qty": 100,
            "committed_delivery_date": "2026-11-20",
        }
        report = summarize_supplier_performance(
            {"purchase_orders": [order]},
            today=date(2026, 11, 23),
        )
        supplier = report["suppliers"][0]
        self.assertEqual(supplier["unverified_completed_pos"], 1)
        self.assertIsNone(supplier["on_time_delivery_rate_pct"])

    def test_supplier_names_are_grouped_case_insensitively(self):
        first = {**self.orders[0], "supplier": "Mill A"}
        second = {**self.orders[3], "supplier": " mill a "}
        report = summarize_supplier_performance(
            {"purchase_orders": [first, second]},
            today=date(2026, 11, 23),
        )
        self.assertEqual(report["total_suppliers"], 1)
        self.assertEqual(report["suppliers"][0]["total_pos"], 2)

    def test_rejects_duplicate_po_numbers_case_insensitively(self):
        duplicate = {**self.orders[0], "po_number": " fl-1001 "}
        with self.assertRaisesRegex(ValueError, "duplicate po_number"):
            summarize_supplier_performance(
                {"purchase_orders": [self.orders[0], duplicate]},
                today=date(2026, 11, 23),
            )

    def test_rejects_invalid_actual_delivery_date(self):
        order = {**self.orders[0], "actual_delivery_date": "23/11/2026"}
        with self.assertRaisesRegex(ValueError, "actual_delivery_date must use YYYY-MM-DD"):
            summarize_supplier_performance(
                {"purchase_orders": [order]},
                today=date(2026, 11, 23),
            )

    def test_rejects_actual_date_for_open_order(self):
        order = {**self.orders[2], "actual_delivery_date": "2026-11-22"}
        with self.assertRaisesRegex(ValueError, "fully received"):
            summarize_supplier_performance(
                {"purchase_orders": [order]},
                today=date(2026, 11, 23),
            )

    def test_rejects_future_actual_delivery_date(self):
        order = {**self.orders[0], "actual_delivery_date": "2026-11-24"}
        with self.assertRaisesRegex(ValueError, "cannot be in the future"):
            summarize_supplier_performance(
                {"purchase_orders": [order]},
                today=date(2026, 11, 23),
            )

    def test_rejects_empty_and_oversized_batches(self):
        for orders in ([], [{}] * 51):
            with self.subTest(count=len(orders)):
                with self.assertRaises(ValueError):
                    summarize_supplier_performance(
                        {"purchase_orders": orders},
                        today=date(2026, 11, 23),
                    )


if __name__ == "__main__":
    unittest.main()
