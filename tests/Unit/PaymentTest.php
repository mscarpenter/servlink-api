<?php

namespace Tests\Unit;

use App\Models\Payment;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    /** @test */
    public function calculates_payment_values_correctly()
    {
        $baseAmount = 100.00;
        $commissionRate = 0.18;

        $values = Payment::calculatePaymentValues($baseAmount, $commissionRate);

        $this->assertEquals(100.00, $values['base_amount']);
        $this->assertEquals(18.00, $values['commission_amount']);
        $this->assertEquals(100.00, $values['professional_pay']);
        $this->assertEquals(118.00, $values['total_charge_establishment']);
    }

    /** @test */
    public function calculates_payment_with_different_commission_rate()
    {
        $baseAmount = 200.00;
        $commissionRate = 0.15;

        $values = Payment::calculatePaymentValues($baseAmount, $commissionRate);

        $this->assertEquals(200.00, $values['base_amount']);
        $this->assertEquals(30.00, $values['commission_amount']);
        $this->assertEquals(230.00, $values['total_charge_establishment']);
    }

    /** @test */
    public function rounds_commission_correctly()
    {
        $baseAmount = 33.33;
        $commissionRate = 0.18;

        $values = Payment::calculatePaymentValues($baseAmount, $commissionRate);

        $this->assertEquals(6.00, $values['commission_amount']); // 33.33 * 0.18 = 5.9994 → 6.00
    }
}
