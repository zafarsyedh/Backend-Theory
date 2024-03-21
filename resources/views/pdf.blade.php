<form action="{{route('compress.pdf')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="pdf_file">
    <button type="submit">Compress PDF</button>
</form>
