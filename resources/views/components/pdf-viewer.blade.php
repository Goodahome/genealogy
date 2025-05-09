<div class="pdf-container">
    <div class="pdf-controls">
        <button id="prev" class="btn btn-sm btn-primary">上一页</button>
        <span id="page-num"></span> / <span id="page-count"></span>
        <button id="next" class="btn btn-sm btn-primary">下一页</button>
        <button id="zoom-in" class="btn btn-sm btn-secondary">放大</button>
        <button id="zoom-out" class="btn btn-sm btn-secondary">缩小</button>
    </div>
    <canvas id="pdf-canvas"></canvas>
</div>

@push('styles')
<style>
    .pdf-container {
        width: 100%;
        margin: 0 auto;
        text-align: center;
    }
    .pdf-controls {
        margin: 10px 0;
        padding: 5px;
        background-color: #f5f5f5;
        border-radius: 5px;
    }
    #pdf-canvas {
        border: 1px solid #ddd;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin: 0 auto;
        display: block;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
<script>
    // PDF.js 工作器路径
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.worker.min.js';
    
    // PDF 文件路径
    const pdfUrl = '{{ $pdfUrl }}';
    
    // 页面元素
    const canvas = document.getElementById('pdf-canvas');
    const ctx = canvas.getContext('2d');
    const pageNum = document.getElementById('page-num');
    const pageCount = document.getElementById('page-count');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
    const zoomInBtn = document.getElementById('zoom-in');
    const zoomOutBtn = document.getElementById('zoom-out');
    
    // 当前页码和缩放比例
    let currentPage = 1;
    let scale = 1.5;
    let pdfDoc = null;
    
    // 渲染PDF页面
    function renderPage(pageNum) {
        pdfDoc.getPage(pageNum).then(function(page) {
            const viewport = page.getViewport({ scale: scale });
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            
            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            
            page.render(renderContext);
        });
        
        // 更新页码显示
        pageNum.textContent = currentPage;
    }
    
    // 加载PDF文件
    pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
        pdfDoc = pdf;
        pageCount.textContent = pdf.numPages;
        
        // 渲染第一页
        renderPage(currentPage);
        
        // 上一页按钮事件
        prevBtn.addEventListener('click', function() {
            if (currentPage <= 1) return;
            currentPage--;
            renderPage(currentPage);
        });
        
        // 下一页按钮事件
        nextBtn.addEventListener('click', function() {
            if (currentPage >= pdfDoc.numPages) return;
            currentPage++;
            renderPage(currentPage);
        });
        
        // 放大按钮事件
        zoomInBtn.addEventListener('click', function() {
            scale += 0.2;
            renderPage(currentPage);
        });
        
        // 缩小按钮事件
        zoomOutBtn.addEventListener('click', function() {
            if (scale <= 0.5) return;
            scale -= 0.2;
            renderPage(currentPage);
        });
    }).catch(function(error) {
        console.error('PDF加载失败:', error);
        canvas.insertAdjacentHTML('afterend', '<div class="alert alert-danger">PDF加载失败，请检查文件路径是否正确。</div>');
    });
</script>
@endpush