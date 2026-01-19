<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Loan Calculator</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body x-data="loanApp()">
  <main class="max-w-5xl mx-auto px-4">
    <h1 class="text-4xl font-bold text-center my-2">Loan Calculator</h1>

    <form class="flex flex-col border border-gray-300 p-4 rounded-xl bg-gray-100" @submit.prevent="calculate()">
      <label class="text-base mt-4 text-gray-600">
        Principal
        <input class="border px-3 py-2 w-72" :class="errors.principal ? 'border-red-500 bg-red-50' : 'border-gray-300'" type="number" step="0.01" x-model="form.principal" required>
      </label>

      <label class="text-base mt-4 text-gray-600">
        Term (months)
        <input class="border px-3 py-2 w-72" :class="errors.termMonths ? 'border-red-500 bg-red-50' : 'border-gray-300'" type="number" step="1" x-model="form.termMonths" required>
      </label>

      <label class="text-base mt-4 text-gray-600">
        APR (%)
        <input class="border px-3 py-2 w-72" :class="errors.apr ? 'border-red-500 bg-red-50' : 'border-gray-300'" type="number" step="0.01" x-model="form.apr" required>
      </label>

      <label class="text-base mt-4 text-gray-600">
        Loan type
        <select class="py-2 px-4 rounded-xl" x-model="form.product" required>
          <option value="annuity">Annuity</option>
          <option value="linear">Linear</option>
        </select>
      </label>

      <button class="max-w-24 rounded-md mt-4 py-2 bg-blue-500 text-white hover:bg-blue-600 disabled:opacity-50" type="submit" :disabled="loading">
        <span x-show="!loading">Calculate</span>
        <span x-show="loading">Calculating…</span>
      </button>
    </form>

    <template x-for="(message, field) in errors" :key="field">
      <div
        class="error flex flex-col border border-gray-300 p-3 rounded-xl bg-red-100 mt-4 text-red-700"
        x-text="message"
      ></div>
    </template>

    <div class="res-row flex mt-4 gap-4 items-start">
      <div class="w-1/4 summary flex flex-col border border-gray-300 rounded-xl bg-gray-100 p-4" x-show="result">
        <h2 class="text-xl mb-4">Results:</h2>
        <div>Payment: <strong x-text="result.payment === null ? 'Variable' : fmt(result.payment)"></strong></div>
        <div>Total repaid: <strong x-text="fmt(result.totalRepayment)"></strong></div>
        <div>Total interest: <strong x-text="fmt(result.totalInterest)"></strong></div>
      </div>

      <div x-show="result && result.schedule" class="flex-1 summary flex-col border border-gray-300 rounded-xl bg-gray-100 p-4">
        <h2 class="text-xl mb-4">Schedule:</h2>
        <table class="w-full border-collapse">
          <thead class="bg-white py-4">
            <tr>
              <th class="border px-2 py-1 text-left">Month</th>
              <th class="border px-2 py-1 text-left">Payment</th>
              <th class="border px-2 py-1 text-left">Interest</th>
              <th class="border px-2 py-1 text-left">Principal</th>
              <th class="border px-2 py-1 text-left">Balance</th>
            </tr>
          </thead>
          <tbody>
          <template x-for="(row, period) in result.schedule" :key="period">
            <tr>
              <td x-text="period" class="text-center"></td>
              <td x-text="fmt(row.payment)" class="text-center"></td>
              <td x-text="fmt(row.interest)" class="text-center"></td>
              <td x-text="fmt(row.principal)" class="text-center"></td>
              <td x-text="fmt(row.balance)" class="text-center"></td>
            </tr>
          </template>
          </tbody>
        </table>
      </div>
    </div>
  </main>

    <script>
      function loanApp() {
        return {
          loading: false,
          error: '',
          result: null,
          form: {
            principal: 100000,
            termMonths: 360,
            apr: 5,
            product: 'annuity',
          },
          errors: {},

          fmt(n) {
            if (n === null || n === undefined) return '';
            return Number(n).toFixed(2);
          },

          async calculate() {
            this.loading = true;
            this.errors = {}; 
            this.result = null;

            try {
              const res = await fetch('/api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.form)
              });

              const data = await res.json();
              if (!res.ok) {
                if (data.errors) {
                  this.errors = data.errors;
                }
                return;
              }

              this.result = data;
            } catch (e) {
              this.error = 'Failed to reach server';
            } finally {
              this.loading = false;
            }
          }
        }
      }
    </script>
</body>
</html>
