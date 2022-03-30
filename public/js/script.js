let ul = document.getElementById("produits");
pForm.addEventListener('submit', async (e) => { e.preventDefault()
//Je récupère les valeurs du formulaire
const data =
    {
        name: document.getElementById('add_produit_name').value, description:
        document.getElementById('add_produit_description').value }
        const response = await fetch('/produit/add', { method: 'POST', headers:
            { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
        const produits = JSON.parse(await response.json()); console.log(produits);
        for(produit of produits){ console.log(produit)
        let li = document.createElement('li')
            li.innerHTML= produit.name
            ul.appendChild(li);
    } })