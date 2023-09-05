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
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '50px' }}>ID</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '180px' }}>Name</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '160px' }}>Username</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '240px' }}>Email</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">
                <div style={{ width: '240px' }}>Country</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">
                <div style={{ width: '80px' }}></div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">
                <div style={{ width: '80px' }}>Created at</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '200px' }} />
              </TextStyle>
            </Stack.Item>
          </Stack>
        </ResourceList.Item>
      </Card>
    );
  }
}

export default Header;
