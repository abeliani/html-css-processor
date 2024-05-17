const sequenceLength = 5;
var unusedVar = 'unused data';
let unusedLetVar = undefined;

/**
 * @param n
 * @returns {number}
 */
function factorial(n) {
    if (n === 0 || n === 1) {
        return 1;
    }
    return n * factorial(n - 1);
}

// returns magic numbers
function fibonacci(sequenceLength) {
    let a = 0, b = 1, temp;
    console.log(a);
    for (let i = 2; i <= sequenceLength; i++) {
        temp = a + b;
        a = b;
        b = temp;
        console.log(a);
    }
}

function printer()
{
    fibonacci(sequenceLength)
    factorial(5)
}

printer()
alert("I wanna to be here!");