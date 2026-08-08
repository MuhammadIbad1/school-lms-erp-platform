import puppeteer from 'puppeteer-core';
import path from 'path';

(async () => {
    try {
        console.log('Launching local Chrome...');
        const browser = await puppeteer.launch({
            executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu']
        });
        const page = await browser.newPage();
        
        console.log('Navigating to http://127.0.0.1:8000/portfolio...');
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
                top: '12mm',
                bottom: '12mm',
                left: '10mm',
                right: '10mm'
            }
        });

        await browser.close();
        console.log('SUCCESS: PDF generated successfully at ' + outputPath);
    } catch (err) {
        console.error('Error generating PDF:', err);
        process.exit(1);
    }
})();
