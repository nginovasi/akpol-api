<?php namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Create an Module in HMVC
 *
 * @package App\Commands
 * @author Mufid Jamaluddin <https://github.com/MufidJamaluddin/Codeigniter4-HMVC>
 */
class ModuleCreate extends BaseCommand
{
    /**
     * The group the command is lumped under
     * when listing commands.
     *
     * @var string
     */
    protected $group       = 'Development';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name        = 'module:create';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Create CodeIgniter HMVC Modules in app/Modules folder';

    /**
     * the Command's usage
     *
     * @var string
     */
    protected $usage        = 'module:create [ModuleName] [Options]';

    /**
     * the Command's Arguments
     *
     * @var array
     */
    protected $arguments    = [ 'ModuleName' => 'Module name to be created' ];

    /**
     * the Command's Options
     *
     * @var array
     */
    protected $options      = [
        '-f' => 'Set module folder inside app path (default Modules)',
        '-v' => 'Set view folder inside app path (default Views/modules/)',
    ];

    /**
     * Module Name to be Created
     */
    protected $module_name;


    /**
     * Module folder (default /Modules)
     */
    protected $module_folder;


    /**
     * View folder (default /View)
     */
    protected $view_folder;


    /**
     * Run route:update CLI
     */
    public function run(array $params)
    {
        helper('inflector');

        $this->module_name = $params[0];

        if(!isset($this->module_name))
        {
            CLI::error("Module name must be set!");
            return;
        }

        $this->module_name = ucfirst($this->module_name);

        $module_folder         = $params['-f'] ?? CLI::getOption('f');
        $this->module_folder   = ucfirst($module_folder ?? 'Modules');

        $view_folder         = $params['-v'] ?? CLI::getOption('v');
        $this->view_folder   = $view_folder ?? 'Views';

        mkdir(APPPATH .  $this->module_folder . '/' . $this->module_name);

        try
        {
            $this->createConfig();
            $this->createController();
            $this->createModel();
            $this->createView();

            CLI::write('Module created!');
        }
        catch (\Exception $e)
        {
            CLI::error($e);
        }
    }

    /**
     * Create Config File
     */
    protected function createConfig()
    {
        $configPath = APPPATH .  $this->module_folder . '/' . $this->module_name . '/Config';

        mkdir($configPath);

        if (!file_exists($configPath . '/Routes.php'))
        {
            $routeName = strtolower($this->module_name);

            $template = "<?php

if(!isset(\$routes))
{ 
    \$routes = \Config\Services::routes(true);
}

\$routes->group('$routeName', ['namespace' => 'App\Modules\\$this->module_name\\Controllers', 'filter' => 'web-auth'], function(\$subroutes){
    \$subroutes->add('action/(:any)','".$this->module_name."Action::$1');
    \$subroutes->add('ajax/(:any)','".$this->module_name."Ajax::$1');
    \$subroutes->add('', '$this->module_name::index');
	\$subroutes->add('(:any)', '$this->module_name::$1');

});";

            file_put_contents($configPath . '/Routes.php', $template);
        }
        else
        {
            CLI::error("Can't Create Routes Config! Old File Exists!");
        }
    }

    /**
     * Create Controller File
     */
    protected function createController()
    {
        $controllerPath = APPPATH .  $this->module_folder . '/' . $this->module_name . '/Controllers';

        mkdir($controllerPath);

        if (!file_exists($controllerPath . '/'.$this->module_name.'.php'))
        {
            $template = "<?php namespace App\Modules\\$this->module_name\\Controllers;

use App\Modules\\$this->module_name\\Models\\".$this->module_name."Model;
use App\Core\BaseController;

class ".$this->module_name." extends BaseController
{
    private \$".strtolower($this->module_name)."Model;

    /**
     * Constructor.
     */
    public function __construct()
    {
        \$this->".strtolower($this->module_name)."Model = new ".$this->module_name."Model();
    }

    public function index()
	{
		return redirect()->to(base_url()); 
	}

    public function test()
    {
        return view('App\\Modules\\$this->module_name\\Views\\test'); 
    }

}
";
            file_put_contents($controllerPath . '/'.$this->module_name.'.php', $template);


            $action = "<?php namespace App\Modules\\$this->module_name\\Controllers;

use App\Modules\\$this->module_name\\Models\\".$this->module_name."Model;
use App\Core\BaseController;

class ".$this->module_name."Action extends BaseController
{
    private \$".strtolower($this->module_name)."Model;

    /**
     * Constructor.
     */
    public function __construct()
    {
        \$this->".strtolower($this->module_name)."Model = new ".$this->module_name."Model();
    }

    public function index()
    {
        return redirect()->to(base_url()); 
    }
}
";
            file_put_contents($controllerPath . '/'.$this->module_name.'Action.php', $action);

            $ajax = "<?php namespace App\Modules\\$this->module_name\\Controllers;

use App\Modules\\$this->module_name\\Models\\".$this->module_name."Model;
use App\Core\BaseController;

class ".$this->module_name."Ajax extends BaseController
{
    private \$".strtolower($this->module_name)."Model;

    /**
     * Constructor.
     */
    public function __construct()
    {
        \$this->".strtolower($this->module_name)."Model = new ".$this->module_name."Model();
    }

    public function index()
    {
        return redirect()->to(base_url()); 
    }
}
";
            file_put_contents($controllerPath . '/'.$this->module_name.'Ajax.php', $ajax);
        }
        else
        {
            CLI::error("Can't Create Controller! Old File Exists!");
        }
    }

    /**
     * Create Models File
     */
    protected function createModel()
    {
        $modelPath = APPPATH .  $this->module_folder . '/' . $this->module_name . '/Models';

        mkdir($modelPath);

        if (!file_exists($modelPath . '/' . $this->module_name . 'Model.php'))
        {

            $template = "<?php namespace App\Modules\\$this->module_name\\Models;

use App\Core\BaseModel;

class ".$this->module_name."Model extends BaseModel
{

}";
            file_put_contents($modelPath . '/'.$this->module_name.'Model.php', $template);
        }
        else
        {
            CLI::error("Can't Create UserModel! Old File Exists!");
        }
    }

    /**
     * Create View
     */
    protected function createView()
    {
        $view_path = APPPATH . $this->module_folder . '/' . $this->module_name . '/Views';
        mkdir($view_path);

        if (!file_exists($view_path . '/test.php'))
        {
            $template = '<section>

	<h1>Test Page</h1>

	<p>If you would like to edit this page you will find it located at:</p>

	<pre><code>app/Views/'. strtolower($this->module_name) .'/dashboard.php</code></pre>

	<p>The corresponding controller for this page can be found at:</p>

	<pre><code>app/Modules/'. $this->module_name .'/Controllers/Dashboard.php</code></pre>

</section>';

            file_put_contents($view_path . '/test.php', $template);
        }
        else
        {
            CLI::error("Can't Create View! Old File Exists!");
        }

    }

}