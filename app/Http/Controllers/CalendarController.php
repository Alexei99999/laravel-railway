namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        // Lógica para obtener eventos de la base de datos
        $events = [
            ['id' => 1, 'title' => 'Reunión CNE', 'start' => $start, 'end' => $end],
            // Agrega más eventos según tu lógica
        ];
        return response()->json($events);
    }

    public function store(Request $request)
    {
        // Lógica para guardar el evento en la base de datos
        $event = $request->all();
        // Simulación de guardado
        return response()->json($event);
    }
}
