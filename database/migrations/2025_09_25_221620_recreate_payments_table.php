<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->text('notes')->nullable();
                $table->date('payment_date');
                $table->timestamps();
            });
        } else {
            // add missing columns to existing table
            if (!Schema::hasColumn('payments', 'tenant_id')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                });
            }

            if (!Schema::hasColumn('payments', 'invoice_id')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
                });
            }

            if (!Schema::hasColumn('payments', 'amount')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->decimal('amount', 10, 2);
                });
            }

            if (!Schema::hasColumn('payments', 'notes')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->text('notes')->nullable();
                });
            }

            if (!Schema::hasColumn('payments', 'payment_date')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->date('payment_date');
                });
            }

            // timestamps: add both if neither exist, otherwise add whichever is missing
            if (!Schema::hasColumn('payments', 'created_at') && !Schema::hasColumn('payments', 'updated_at')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->timestamps();
                });
            } else {
                if (!Schema::hasColumn('payments', 'created_at')) {
                    Schema::table('payments', function (Blueprint $table) {
                        $table->timestamp('created_at')->nullable();
                    });
                }
                if (!Schema::hasColumn('payments', 'updated_at')) {
                    Schema::table('payments', function (Blueprint $table) {
                        $table->timestamp('updated_at')->nullable();
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('payments');

        // Undo up() changes
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (Schema::hasColumn('payments', 'tenant_id')) {
                    $table->dropForeign(['tenant_id']);
                    $table->dropColumn('tenant_id');
                }

                if (Schema::hasColumn('payments', 'invoice_id')) {
                    $table->dropForeign(['invoice_id']);
                    $table->dropColumn('invoice_id');
                }

                if (Schema::hasColumn('payments', 'amount')) {
                    $table->dropColumn('amount');
                }

                if (Schema::hasColumn('payments', 'notes')) {
                    $table->dropColumn('notes');
                }

                if (Schema::hasColumn('payments', 'payment_date')) {
                    $table->dropColumn('payment_date');
                }

                if (Schema::hasColumn('payments', 'created_at')) {
                    $table->dropColumn('created_at');
                }

                if (Schema::hasColumn('payments', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
};
