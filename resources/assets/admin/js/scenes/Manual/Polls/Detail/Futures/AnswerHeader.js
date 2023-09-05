import React, { Component } from 'react';
import {
  Card, Stack, ResourceList, TextStyle
} from '@shopify/polaris';

class Header extends Component {
  render() {
    return (
      <Card>
        <ResourceList.Item>
          <Stack>
            <Stack.Item fill>
              <div style={{ width: '120px'}}>
                <TextStyle variation="strong">Name</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item fill>
              <div style={{ width: '80px'}}>
                <TextStyle variation="strong">Abbr</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item fill>
              <div style={{ width: '60px'}}>
                <TextStyle variation="strong">Score</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item fill>
              <div style={{ width: '100px'}}>
                <TextStyle variation="strong">Standing</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item fill>
              <div style={{ width: '60px'}}>
                <TextStyle variation="strong">Odds</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item fill>
              <div style={{ width: '100px'}}>
                <TextStyle variation="strong">Points</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item fill>
              <div style={{ width: '60px'}}>
                <TextStyle variation="strong">Absent</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item>
              <div style={{ width: '80px' }}>
                <TextStyle variation="strong">Action</TextStyle>
              </div>
            </Stack.Item>
          </Stack>
        </ResourceList.Item>
      </Card>
    );
  }
}

export default Header;
