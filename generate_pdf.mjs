import puppeteer from 'puppeteer';
import path from 'path';

(async () => {
    try {
        console.log('Launching headless browser...');
        const browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });
        const page = await browser.newPage();
        
        console.log('Navigating to portfolio page...');
        await page.goto('http://127.0.0.1:8000/portfolio', {
            waitUntil: 'networkidle0',
            timeout: 60000
        });

        const outputPath = path.resolve('public', 'EduNova_School_LMS_Portfolio.pdf');
        console.log('Generating PDF at:', outputPath);

        await page.pdf({
            path: outputPath,
            format: 'A4',
            printBackground: true,
            margin: {
                top: '15mm',
                bottom: '15mm',
                left: '12mm',
                right: '12mm'
            }
        });

        await browser.close();
        console.log('PDF generated successfully!');
    } catch (err) {
        console.error('Error generating PDF:', err);
        process.exit(1);
    }
})();
