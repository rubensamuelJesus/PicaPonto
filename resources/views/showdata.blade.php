<!DOCTYPE html>
<html>
<body>
<title>Raspberry PI Data Logs</title>
<h1>Raspberry PI Data Logs</h1>
<table style="width:100%">
 <tr>
   <td>Date</td>
   <td>time</td>
   <td>Cpu Usage % </td>
   <td>Status</td>
 </tr>
 <tr>
 @foreach($data as $values)
 <tr>
   <td> {{ $values->date }}</td>
   <td> {{ $values->time }}</td>
   <td> {{ $values->usage }}</td>
   <td> {{ $values->status }}</td>
 </tr>
 @endforeach
</table>
</body>
</html>