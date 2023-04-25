<div>
   Here's a summary of all the artists and their albums:
</div>

<div>
   @foreach ($artists as $artist)
      <div>
         <h2>{{ $artist->name }}</h2>
         <h3>Albums</h3>
         <ul>
            @foreach ($artist->albums as $album)
               <li>{{ $album->name }}</li>
            @endforeach
         </ul>
      </div>
   @endforeach
</div>