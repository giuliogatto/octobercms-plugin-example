<?php namespace Gatto\Jobs\Models;

use Model;

/**
 * Appointment Model
 */
class Appointment extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'gatto_jobs_appointments';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['customer_id','engineer_id','job_id','price','appointment_date','time_slot',
    'done','paid','notes'];

    /**
     * @var array Validation rules
     */
    public $rules = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'engineer' => ['Backend\Models\User', 'foreignKey' => 'engineer_id'],
        'customer' => ['Rainlab\User\Models\User', 'foreignKey' => 'customer_id'],
        'job' => ['Gatto\Jobs\Models\Job', 'foreignKey' => 'job_id'],
        'timeslot' => ['Gatto\Jobs\Models\Timeslot', 'foreignKey' => 'time_slot']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function appointmentjob()
    {
        return $this->belongsTo('Gatto\Jobs\Models\Job',  'job_id');
    }

}



?>



<?php namespace Gatto\Jobs\Models;

use Model;

/**
 * Job Model
 */
class Job extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'gatto_jobs_jobs';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['name','price','duration','is_recurring','days_of_recursion','description',
    'amount_inc_vat','amount_ex_vat','vat_rate','notice','appointment_delay_days'];

    /**
     * @var array Validation rules
     */
    public $rules = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}

?>



<?php namespace Gatto\Jobs\Models;

use Model;

/**
 * Qualification Model
 */
class Qualification extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'gatto_jobs_engineer_required_qualifications';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['job_id','name_of_qualification'];

    /**
     * @var array Validation rules
     */
    public $rules = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'job' => ['Gatto\Jobs\Models\Job', 'foreignKey' => 'job_id']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function certificationsnames()
    {
        return $this->belongsTo('Gatto\Jobs\Models\Job',  'job_id');
    }


}



?>


This is a sample controller for the backend of the app:
Each qualification belongs to a job. 
In the index method of the controller the routed parameter (from the URL) call an October ListController behaviour.



<?php namespace Gatto\Jobs\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

class Qualifications extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = ['gatto.jobs.jobs'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Gatto.Jobs', 'Jobs', 'qualifications');
    }

    protected $jobId;

    public function index($jobId = null)
    {
        // Store the routed parameter to use later
        $this->jobId = $jobId;

        // Call the ListController behavior standard functionality
        $this->asExtension('ListController')->index();
    }

    public function listExtendQuery($query)
    {
       // Extend the list query to filter by the qualification id
        if ($this->jobId)
            $query->where('job_id', $this->jobId);
    }
}



?>


This is a the update.htm view relative to the Qualifications controller:



<?php Block::put('breadcrumb') ?>
    <ul>
        <li><a href="<?= Backend::url('gatto/jobs/qualifications') ?>">Qualifications</a></li>
        <li><?= e($this->pageTitle) ?></li>
    </ul>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <?= Form::open(['class'=>'layout-item stretch layout-column']) ?>

        <?= $this->formRender() ?>

        

        <div class="form-buttons layout-item fix">
            <div class="loading-indicator-container">
                <button
                    type="submit"
                    data-request="onSave"
                    data-request-data="redirect:0"
                    data-hotkey="ctrl+s"
                    data-hotkey-mac="cmd+s"
                    data-load-indicator="Saving Qualification..."
                    class="btn btn-primary">
                    <u>S</u>ave
                </button>
                <button
                    type="button"
                    data-request="onSave"
                    data-request-data="close:1"
                    data-hotkey="ctrl+enter"
                    data-hotkey-mac="cmd+enter"
                    data-load-indicator="Saving Qualification..."
                    class="btn btn-default">
                    Save and Close
                </button>
                <button
                    type="button"
                    class="oc-icon-trash-o btn-icon danger pull-right"
                    data-request="onDelete"
                    data-load-indicator="Deleting Qualification..."
                    data-request-confirm="Do you really want to delete this Qualification?">
                </button>
                <span class="btn-text">
                    or <a href="<?= Backend::url('gatto/jobs/qualifications') ?>">Cancel</a>
                </span>
            </div>
        </div>

    <?= Form::close() ?>

<?php else: ?>

    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p><a href="<?= Backend::url('gatto/jobs/qualifications') ?>" class="btn btn-default">Return to Qualifications list</a></p>

<?php endif ?>





This is a query for the front-end, using Laravel query builder:
it retrieves all the appointments for the logged in customer.

<?
$this['appointments'] = Appointments::where('customer_id', '=', Auth::getUser()->id)
            ->where('gatto_jobs_appointments.id', '=', $app_id)
            ->join('gatto_jobs_jobs', 'gatto_jobs_appointments.job_id', '=', 'gatto_jobs_jobs.id')
            ->join('users', 'gatto_jobs_appointments.customer_id', '=', 'users.id')
            ->join('gatto_jobs_timeslots', 'gatto_jobs_timeslots.id', '=', 'time_slot')
            ->get(['gatto_jobs_appointments.*',
                'gatto_jobs_jobs.name as name',
                'gatto_jobs_timeslots.name as timeslot',
                'users.name as username' ])
            ->toArray();


?>

This is a simple Eloquent query to find a Job from an ID:
<?
$this['selected_job'] = Job::where('id','=',$this['job_id'])
                          ->first()
                          ->toArray();

?>
