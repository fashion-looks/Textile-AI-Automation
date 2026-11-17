import unittest

from ecosystem.garment_costing import calculate_fabric_plan


class GarmentSizeMixTests(unittest.TestCase):
    def setUp(self):
        self.payload = {
            "style_code": "FL-TEE-002",
            "fabric_name": "Single Jersey 180 GSM",
            "order_qty": 100,
            "size_breakdown": [
                {"size": "S", "order_qty_pcs": 20, "consumption_m_per_piece": 1.1},
                {"size": "M", "order_qty_pcs": 40, "consumption_m_per_piece": 1.2},
                {"size": "L", "order_qty_pcs": 30, "consumption_m_per_piece": 1.3},
                {"size": "XL", "order_qty_pcs": 10, "consumption_m_per_piece": 1.4},
            ],
            "wastage_percent": 5,
            "rate_per_meter": 100,
        }

    def test_calculates_weighted_size_mix_and_cost(self):
        result = calculate_fabric_plan(self.payload)
        self.assertEqual(result["consumption_basis"], "size_breakdown")
        self.assertEqual(result["consumption_m_per_piece"], "1.2300")
        self.assertEqual(result["base_meters"], "123.00")
        self.assertEqual(result["wastage_meters"], "6.15")
        self.assertEqual(result["planned_meters"], "129.15")
        self.assertEqual(result["total_fabric_cost"], "12915.00")
        self.assertEqual(result["fabric_cost_per_piece"], "129.15")
        self.assertEqual(result["size_breakdown"][0]["base_meters_exact"], "22.0")

    def test_legacy_single_consumption_mode_is_unchanged(self):
        payload = {
            "style_code": "FL-TEE-001",
            "fabric_name": "Jersey",
            "order_qty": 100,
            "consumption_m_per_piece": 1.2,
            "wastage_percent": 5,
            "rate_per_meter": 100,
        }
        result = calculate_fabric_plan(payload)
        self.assertEqual(result["consumption_basis"], "single_rate")
        self.assertEqual(result["planned_meters"], "126.00")
        self.assertNotIn("size_breakdown", result)

    def test_rejects_size_quantities_that_do_not_match_order(self):
        payload = {**self.payload, "size_breakdown": self.payload["size_breakdown"][:-1]}
        with self.assertRaisesRegex(ValueError, "must sum to order_qty"):
            calculate_fabric_plan(payload)

    def test_rejects_duplicate_size_labels_case_insensitively(self):
        rows = [*self.payload["size_breakdown"],
                {"size": "s", "order_qty_pcs": 1, "consumption_m_per_piece": 1.1}]
        payload = {**self.payload, "order_qty": 101, "size_breakdown": rows}
        with self.assertRaisesRegex(ValueError, "duplicate sizes"):
            calculate_fabric_plan(payload)

    def test_rejects_both_single_rate_and_size_mix(self):
        payload = {**self.payload, "consumption_m_per_piece": 1.2}
        with self.assertRaisesRegex(ValueError, "either consumption_m_per_piece or size_breakdown"):
            calculate_fabric_plan(payload)

    def test_rejects_boolean_or_fractional_size_quantity(self):
        for value in (True, 2.5, 0):
            rows = [dict(self.payload["size_breakdown"][0],
                         order_qty_pcs=value), *self.payload["size_breakdown"][1:]]
            with self.subTest(value=value):
                with self.assertRaises(ValueError):
                    calculate_fabric_plan({**self.payload, "size_breakdown": rows})

    def test_rejects_invalid_or_non_finite_size_consumption(self):
        for value in (0, 101, float("nan"), True):
            rows = [dict(self.payload["size_breakdown"][0],
                         consumption_m_per_piece=value), *self.payload["size_breakdown"][1:]]
            with self.subTest(value=value):
                with self.assertRaises(ValueError):
                    calculate_fabric_plan({**self.payload, "size_breakdown": rows})

    def test_rejects_empty_or_oversized_size_breakdown(self):
        for rows in ([], self.payload["size_breakdown"] * 6):
            with self.subTest(count=len(rows)):
                with self.assertRaises(ValueError):
                    calculate_fabric_plan({**self.payload, "size_breakdown": rows})

    def test_rejects_non_object_rows_and_invalid_size_labels(self):
        with self.assertRaisesRegex(ValueError, "must be an object"):
            calculate_fabric_plan({**self.payload, "size_breakdown": ["S"]})
        rows = [dict(self.payload["size_breakdown"][0], size=" "), *self.payload["size_breakdown"][1:]]
        with self.assertRaises(ValueError):
            calculate_fabric_plan({**self.payload, "size_breakdown": rows})


if __name__ == "__main__":
    unittest.main()
