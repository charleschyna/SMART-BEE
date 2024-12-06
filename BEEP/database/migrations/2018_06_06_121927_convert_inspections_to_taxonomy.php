<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage; // Add this
use Illuminate\Support\Facades\DB; // Add this
use App\ChecklistFactory;
use App\User;
use App\Inspection;
use App\Role;

class ConvertInspectionsToTaxonomy extends Migration
{
    /**
     * Property for checklist factory and debug mode
     */
    protected $checklistFactory; // Define the visibility
    protected $debug; // Define the visibility

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->checklistFactory = new ChecklistFactory;
        $this->debug = false;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('categories')) 
        {
            echo("ConvertInspectionsToTaxonomy disableForeignKeyConstraints...\r\n");
            Schema::disableForeignKeyConstraints();

            if (Schema::hasTable('inspections') == false || Inspection::all()->count() == 0)
            {
                // Use Storage facade to retrieve the SQL file
                $sql = Storage::get('new_taxonomy_tables.sql');
                if ($sql)
                {
                    echo("ConvertInspectionsToTaxonomy replacing new db-tables with test.beep db tables...\r\n");
                    echo(DB::unprepared($sql)."\r\n");
                }
                else
                {
                    echo("ConvertInspectionsToTaxonomy - ERROR - No SQL found, NOT replaced new db-tables\r\n");
                }
            }
            else
            {
                echo("ConvertInspectionsToTaxonomy already replaced db tables...\r\n");
            }

            // add missing roles and permissions
            Role::updateRoles();

            // Convert old category_ids and user data
            if (Schema::hasTable('actions') && Schema::hasTable('conditions'))
            {
                echo("ConvertInspectionsToTaxonomy converting user data...\r\n");
                
                $users = $this->debug 
                    ? User::where('id', '<=', 2)->get() 
                    : User::all();

                $this->checklistFactory->convertUsersChecklists($users, $this->debug);
                
                if(!$this->debug)
                {
                    Schema::dropIfExists('actions');
                    Schema::dropIfExists('conditions');
                }
            }
            else
            {
                echo("ConvertInspectionsToTaxonomy - ERROR - No table 'categories' found!");
            }

            // Modify foreign keys
            if (Schema::hasTable('hive_types'))
            {
                Schema::table('hives', function (Blueprint $table) 
                {
                    $table->dropForeign(['hive_type_id']);
                    $table->foreign('hive_type_id')->references('id')->on('categories')->onUpdate('cascade');
                });
                Schema::dropIfExists('hive_types');
            }
            if (Schema::hasTable('bee_races'))
            {
                Schema::table('queens', function (Blueprint $table) 
                {
                    $table->dropForeign(['race_id']);
                    $table->foreign('race_id')->references('id')->on('categories')->onUpdate('cascade');
                });
                Schema::dropIfExists('bee_races');
            }

            echo("ConvertInspectionsToTaxonomy enableForeignKeyConstraints...\r\n");
            Schema::enableForeignKeyConstraints();
        }
        else
        {
            echo("ConvertInspectionsToTaxonomy - ERROR - table actions, conditions, or categories not available (already migrated)\r\n");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
