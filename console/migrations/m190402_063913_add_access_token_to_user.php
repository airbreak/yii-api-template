<?php

use yii\db\Migration;

/**
 * Class m190402_063913_add_access_token_to_user
 */
class m190402_063913_add_access_token_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190402_063913_add_access_token_to_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190402_063913_add_access_token_to_user cannot be reverted.\n";

        return false;
    }
    */
}
