# BRACKETS BALANCING EVALUATOR

<br>

Brackets Balancing Evaluator can tell if a sequence of brackets is balanced or not. Each of the following characters is a bracket: ```()[]{}```.

<br>

## Rules to have balanced brackets

1. "Pairs": An opened bracket must be closed to be valid, which means they must form pairs;
2. "Scope": A pair must have both parts inside another pair or outside any pair, which means that a pair opening inside another pair and closing outside of it is not valid.
 
<br>

<b>Examples of valid pairs:</b>

- ```(){}[]```
- ```[{()}](){}```

<br>

<b>Examples of not valid pairs:</b>

- ```[{)]```, ```{``` and ```)``` don't have a pair, the first one isn't closed and the second one isn't opened;
- ```[]{()```, ```{``` doesn't have a pair, it isn't closed;
- ```{}[(])```, ```(``` does have a pair, but their parts open and close on different scopes.

<br>

<b>Observation:</b>

When there is ambiguity concerning the opening and closing of a pair, precedence is given to the closer ones.

<br>

## How it works

After receiving a sequence of brackets, its content is filtered, keeping only brackets and excluding everything else.

For example, the sequence ```{asd(*871'2)}[]``` is valid, even though it includes foreign characters.

For this sequence, the first thing shown is the characters count:

```
string_received (count: 15) {
   {asd(*871'2)}[]
}
```

<br>

After that, the filtered sequence of brackets is shown:

```
valid_brackets (count: 6) {
   { -- position: 0 
   ( -- position: 4 
   ) -- position: 11 
   } -- position: 12 
   [ -- position: 13 
   ] -- position: 14 
}
```

<br>

Followed by all the opening and closing brackets:

```
brackets_open_order (count: 3) {
   { -- position: 0 
   ( -- position: 4 
   [ -- position: 13 
}

brackets_close_order (count: 3) {
   ) -- position: 11 
   } -- position: 12 
   ] -- position: 14 
}
```

<br>

In this case, as the sequence of brackets is balanced, we get the message that ```The sequence of brackets received is valid.```.

<br>

When a sequence with unbalanced brackets is provided, additional information is shown. The first one is which brackets are orphan, i.e., are unmatched. In this example, the following sequence is used: ```})){()()}```.

```
orphan_brackets (count: 3) {
   } -- position: 0 
   ) -- position: 1 
   ) -- position: 2 
}
```

<br>

After the orphan brackets, it is also shown, first, the inline sequence, highlighting in red the unmatched brackets and the matched ones in blue. Finally, it is shown the sequence in a hierarquical form, which can help visualizing when a pair opens and closes.

```
brackets_result_in_line (count: 9) {
   })){()()}
}

brackets_result_with_visual_aid (count: 9) {
   }))
   {
      ()
      ()
   }
}
```

<br>

If the user doesn't have a sequence of brackets readily available, it is possible to generate sequences with up to 60 characters, including only ```(```, ```)```, ```[```, ```]```, ```{```, ```}```. To do that, it only takes clicking on the "Random" button.


<br>

## How to use it in your server

Clone the repository where you want to use it (using ```git clone https://github.com/silviosomer/brackets-balancing.git```). It will work without any further configuration, besides having Apache (or Nginx) and PHP installed.

