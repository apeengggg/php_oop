function dataChart(data){
    let label_chart = [];
    let data_chart_total = [];
    let data_chart_total_sold = [];
    let data_chart_total_percent = [];
    
    
    for(let i = 0; i < data.length; i++){
        label_chart.push(data[i].event_name)
        data_chart_total.push(parseFloat(data[i].total_ticket))
        data_chart_total_sold.push(parseFloat(data[i].ticket_sold))
        data_chart_total_percent.push(data[i].ticket_sold_percent)
    }
    console.log("🚀 ~ dataChart ~ data_chart_total:", data_chart_total)

    const chart = {
        labels: label_chart,
        datasets: [
            {
                label: 'Total Ticket',
                data: data_chart_total,
                backgroundColor: [
                    'rgba(265, 99, 132, 0.2)',
                    'rgba(64, 162, 235, 0.2)',
                    'rgba(265, 206, 86, 0.2)',
                    'rgba(85, 192, 192, 0.2)',
                    'rgba(163, 102, 255, 0.2)',
                    'rgba(265, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            },
            {
                label: 'Total Ticket Sold',
                data: data_chart_total_sold,
                backgroundColor: [
                    'rgba(265, 99, 132, 0.2)',
                    'rgba(64, 162, 235, 0.2)',
                    'rgba(265, 206, 86, 0.2)',
                    'rgba(85, 192, 192, 0.2)',
                    'rgba(163, 102, 255, 0.2)',
                    'rgba(265, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            },
            {
                label: 'Total Ticket Sold (%)',
                data: data_chart_total_percent,
                backgroundColor: [
                    'rgba(265, 99, 132, 0.2)',
                    'rgba(64, 162, 235, 0.2)',
                    'rgba(265, 206, 86, 0.2)',
                    'rgba(85, 192, 192, 0.2)',
                    'rgba(163, 102, 255, 0.2)',
                    'rgba(265, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            },
        ]
    }

    const config = {
        type: 'bar',
        data: chart,
        options: {
            plugins: {
                legend: {
                    display: false
                },
            }
        }
    }

    return config
}

async function search(){
    commonJS.loading(true)

    await commonJS.get('/dashboard-admin/get', async (response)=> {
        console.log("🚀 ~ commonJS.get ~ response:", response)
        if(response.status == 200){
            const event_chart = dataChart(response.data.data_event)
            new Chart($('#eventChart'), event_chart)
        }else{
            commonJS.toast(response.message, true)
        }
    }, (error) => {
        console.error(error)
    })
    commonJS.loading(false)
}

$(async function (){
    await search()
    await commonJS.setupPermission("D001");
});