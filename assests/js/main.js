/*=============== WIJS VERBERGEN WACHTWOORD LOG IN ===============*/
const passwordAccess = (loginPass, loginEye) =>{
    const input = document.getElementById(loginPass),
          iconEye = document.getElementById(loginEye)
 
    iconEye.addEventListener('click', () =>{
       // Verander wachtwoord naar tekst
       input.type === 'password' ? input.type = 'text'
                                       : input.type = 'password'
 
       
       iconEye.classList.toggle('ri-eye-fill')
       iconEye.classList.toggle('ri-eye-off-fill')
    })
 }
 passwordAccess('password','loginPassword')
 
 /*=============== WIJS VERBERGEN WACHTWOORD MAAK ACCOUNT ===============*/
 const passwordRegister = (loginPass, loginEye) =>{
    const input = document.getElementById(loginPass),
          iconEye = document.getElementById(loginEye)
 
    iconEye.addEventListener('click', () =>{
       // Verander wachtwoord naar tekst
       input.type === 'password' ? input.type = 'text'
                                       : input.type = 'password'
 
       
       iconEye.classList.toggle('ri-eye-fill')
       iconEye.classList.toggle('ri-eye-off-fill')
    })
 }
 passwordRegister('passwordCreate','loginPasswordCreate')
 
 /*=============== WIJS VERBERGEN LOG IN & MAAK ACCOUNT ===============*/
 const loginAcessRegister = document.getElementById('loginAccessRegister'),
       buttonRegister = document.getElementById('loginButtonRegister'),
       buttonAccess = document.getElementById('loginButtonAccess')
 
 buttonRegister.addEventListener('click', () => {
    loginAcessRegister.classList.add('active')
 })
 
 buttonAccess.addEventListener('click', () => {
    loginAcessRegister.classList.remove('active')
 })
